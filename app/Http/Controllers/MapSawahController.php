<?php

// ============================================================
// File: app/Http/Controllers/MapSawahController.php
// ============================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Kabupaten;
use App\Models\LuasBakuSawah;
use App\Models\RekapBulananProduksi;
use App\Models\RekapBulananTanam;
use App\Models\RekapBulananPanen;
use App\Models\KsaLuasTanam;
use App\Models\KsaLuasPanen;
use App\Models\KsaProduksi;
use App\Models\IndeksPertanamanPadi;
use App\Models\RekapTahunanTanam;
use App\Models\RekapTahunanPanen;

class MapSawahController extends Controller
{
    // ─── Halaman utama ────────────────────────────────────────
    public function index()
    {
        return view('map');
    }

    // ─── GeoJSON Kabupaten via controller (agar bisa skip ngrok) ─
    public function geojsonKabupaten()
    {
        $path = public_path('data/sumsel_kab.geojson');

        if (!file_exists($path)) {
            return response()->json(['error' => 'File GeoJSON kabupaten tidak ditemukan'], 404);
        }

        return response()->file($path, [
            'Content-Type'                => 'application/geo+json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    // ─── GeoJSON Kecamatan via controller (agar bisa skip ngrok) ─
    public function geojsonKecamatan()
    {
        $path = public_path('data/sumsel_kec.geojson');

        if (!file_exists($path)) {
            return response()->json(['error' => 'File GeoJSON kecamatan tidak ditemukan'], 404);
        }

        return response()->file($path, [
            'Content-Type'                => 'application/geo+json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    // ─── Available Filters API Endpoint ─────────────────────────────────
    public function availableFilters(Request $request)
    {
        $jenis_data = $request->input('jenis_data', 'ltt');
        $periode = $request->input('periode', 'tahunan');
        
        $years = [];
        $months = [];
        $dates = [];

        // 1. HARIAN
        if ($periode === 'harian' && in_array($jenis_data, ['ltt', 'ltp'])) {
            $table = ($jenis_data === 'ltt') ? 'rekap_harian_tanam' : 'rekap_harian_panen';
            $baseDates = DB::table($table)->select('tanggal')->distinct()->orderBy('tanggal', 'desc')->pluck('tanggal')->toArray();
            $dates = [];
            foreach ($baseDates as $d) {
                $carbon = \Carbon\Carbon::parse($d);
                $daysInMonth = $carbon->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $dates[] = $carbon->copy()->day($i)->format('Y-m-d');
                }
            }
        }

        // 2. BULANAN
        if ($periode === 'bulanan') {
            $modelMap = [
                'ltt' => RekapBulananTanam::class,
                'ltp' => RekapBulananPanen::class,
                'ksa_tanam' => KsaLuasTanam::class,
                'ksa_panen' => KsaLuasPanen::class,
                'ksa_produksi' => KsaProduksi::class,
            ];
            if (isset($modelMap[$jenis_data])) {
                $model = $modelMap[$jenis_data];
                $years = $model::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
                
                $isKsa = str_starts_with($jenis_data, 'ksa');
                if ($isKsa) {
                    $months = $model::select('bulan')->distinct()->pluck('bulan')->map(fn($v) => strtolower($v))->toArray();
                } else {
                    // For RekapBulananTanam/Panen, months are fixed columns. We just return all 12.
                    $months = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
                }
            }
        }

        // 3. TAHUNAN
        if ($periode === 'tahunan') {
            $modelMap = [
                'ltt' => RekapTahunanTanam::class,
                'ltp' => RekapTahunanPanen::class,
                'ksa_tanam' => KsaLuasTanam::class,
                'ksa_panen' => KsaLuasPanen::class,
                'ksa_produksi' => KsaProduksi::class,
                'ip_padi' => IndeksPertanamanPadi::class,
                'lbs' => LuasBakuSawah::class,
            ];
            if (isset($modelMap[$jenis_data])) {
                $model = $modelMap[$jenis_data];
                $years = $model::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
            }
        }

        // Default fallbacks if empty
        if (empty($years) && in_array($periode, ['bulanan', 'tahunan'])) $years = [date('Y')];
        if (empty($dates) && $periode === 'harian') $dates = [date('Y-m-d')];
        if (empty($months) && $periode === 'bulanan') $months = ['januari'];

        return response()->json([
            'years' => array_values(array_unique($years)),
            'months' => array_values(array_unique($months)),
            'dates' => array_values(array_unique($dates))
        ]);
    }

    // ─── Dynamic Data API Endpoint ────────────────────────────────────
    public function dynamicData(Request $request)
    {
        $jenis_data = $request->input('jenis_data', 'ltt'); // ltt, ltp, ksa_tanam, ksa_panen, ksa_produksi, ip_padi, lbs
        $periode = $request->input('periode', 'tahunan'); // harian, bulanan, tahunan
        
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $bulan = strtolower($request->input('bulan', 'januari'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $result = [];

        // 1. HARIAN (hanya LTT dan LTP) -> Level KECAMATAN
        if ($periode === 'harian' && in_array($jenis_data, ['ltt', 'ltp'])) {
            $table = ($jenis_data === 'ltt') ? 'rekap_harian_tanam' : 'rekap_harian_panen';
            
            $parsedDate = \Carbon\Carbon::parse($tanggal);
            $baseTanggal = $parsedDate->copy()->startOfMonth()->format('Y-m-d');
            $dayField = 'tgl_' . $parsedDate->day;
            
            $data = DB::table($table . ' as rh')
                ->join('kecamatan as kec', 'kec.id', '=', 'rh.kecamatan_id')
                ->join('kabupaten as kab', 'kab.id', '=', 'kec.kabupaten_id')
                ->where('rh.tanggal', $baseTanggal)
                ->selectRaw("
                    kec.id          AS kecamatan_id,
                    kec.nama_kecamatan,
                    kab.id          AS kabupaten_id,
                    kab.nama_kabupaten,
                    SUM(rh.{$dayField}) AS value
                ")
                ->groupBy('kec.id', 'kec.nama_kecamatan', 'kab.id', 'kab.nama_kabupaten')
                ->get();

            foreach ($data as $row) {
                $key = $this->normalizeName($row->nama_kecamatan);
                $result[$key] = [
                    'value'          => (float) ($row->value ?? 0),
                    'tanggal'        => $tanggal,
                    'kabupaten_id'   => (string) $row->kabupaten_id,
                    'nama_kabupaten' => $row->nama_kabupaten,
                    'nama_kecamatan' => $row->nama_kecamatan
                ];
            }
            return response()->json(['level' => 'kecamatan', 'data' => $result]);
        }

        // 2. BULANAN -> Level KABUPATEN
        if ($periode === 'bulanan') {
            // Khusus LTT/LTP bulanan kita sum dari data harian untuk mendapatkan detail reguler, oplah, gogo, csr
            if (in_array($jenis_data, ['ltt', 'ltp'])) {
                $table = ($jenis_data === 'ltt') ? 'rekap_harian_tanam' : 'rekap_harian_panen';
                $totalField = ($jenis_data === 'ltt') ? 'total_tanam' : 'total_panen';
                $valField = ($jenis_data === 'ltt') ? 'total_ltt' : 'total_ltp';
                
                $months = ['januari'=>1,'februari'=>2,'maret'=>3,'april'=>4,'mei'=>5,'juni'=>6,'juli'=>7,'agustus'=>8,'september'=>9,'oktober'=>10,'november'=>11,'desember'=>12];
                $monthNum = $months[$bulan] ?? 1;
                
                $data = DB::table($table . ' as rh')
                    ->join('kabupaten as kab', 'kab.id', '=', 'rh.kabupaten_id')
                    ->whereYear('rh.tanggal', $tahun)
                    ->whereMonth('rh.tanggal', $monthNum)
                    ->selectRaw("
                        kab.id          AS kabupaten_id,
                        kab.nama_kabupaten,
                        SUM(rh.{$totalField}) AS reguler,
                        SUM(rh.oplah) AS oplah,
                        SUM(rh.gogo) AS gogo,
                        SUM(rh.csr) AS csr,
                        SUM(rh.{$valField}) AS value
                    ")
                    ->groupBy('kab.id', 'kab.nama_kabupaten')
                    ->get();
                
                // Initialize all kabupatens
                Kabupaten::orderBy('id')->get()->each(function ($kab) use (&$result) {
                    $result[(string) $kab->id] = [
                        'nama_kabupaten' => $kab->nama_kabupaten,
                        'value'          => 0,
                        'reguler'        => 0,
                        'oplah'          => 0,
                        'gogo'           => 0,
                        'csr'            => 0
                    ];
                });

                foreach ($data as $row) {
                    $result[(string) $row->kabupaten_id] = [
                        'nama_kabupaten' => $row->nama_kabupaten,
                        'value'          => (float) ($row->value ?? 0),
                        'reguler'        => (float) ($row->reguler ?? 0),
                        'oplah'          => (float) ($row->oplah ?? 0),
                        'gogo'           => (float) ($row->gogo ?? 0),
                        'csr'            => (float) ($row->csr ?? 0)
                    ];
                }
                return response()->json(['level' => 'kabupaten', 'data' => $result]);
            }

            $modelMap = [
                'ksa_tanam' => KsaLuasTanam::class, // KSA assumes 'bulan' column exists
                'ksa_panen' => KsaLuasPanen::class,
                'ksa_produksi' => KsaProduksi::class,
            ];

            if (!isset($modelMap[$jenis_data])) {
                return response()->json(['level' => 'kabupaten', 'data' => []]);
            }

            $model = $modelMap[$jenis_data];
            $query = $model::where('tahun', $tahun);
            
            // Kolom nilainya beda-beda (misal KSA: 'bulan' => string)
            $isKsa = str_starts_with($jenis_data, 'ksa');
            if ($isKsa) {
                // KsaLuasTanam, KsaLuasPanen punya 'bulan'. KsaProduksi juga punya 'bulan'.
                $query->where('bulan', ucfirst($bulan));
            }

            $records = $query->get()->groupBy('kabupaten_id');

            Kabupaten::orderBy('id')->get()->each(function ($kab) use (&$result, $records, $isKsa, $bulan, $jenis_data) {
                $kabRecords = $records->get($kab->id);
                $val = 0;
                if ($kabRecords) {
                    if ($isKsa) {
                        $colMap = [
                            'ksa_tanam' => 'luas_tanam',
                            'ksa_panen' => 'luas_panen',
                            'ksa_produksi' => 'produksi'
                        ];
                        $col = $colMap[$jenis_data];
                        $val = $kabRecords->sum($col);
                    }
                }
                
                $result[(string) $kab->id] = [
                    'nama_kabupaten' => $kab->nama_kabupaten,
                    'value'          => (float) $val
                ];
            });

            return response()->json(['level' => 'kabupaten', 'data' => $result]);
        }

        // 3. TAHUNAN -> Level KABUPATEN
        if ($periode === 'tahunan') {
            // Khusus LTT/LTP tahunan kita sum dari data harian untuk mendapatkan detail reguler, oplah, gogo, csr
            if (in_array($jenis_data, ['ltt', 'ltp'])) {
                $table = ($jenis_data === 'ltt') ? 'rekap_harian_tanam' : 'rekap_harian_panen';
                $totalField = ($jenis_data === 'ltt') ? 'total_tanam' : 'total_panen';
                $valField = ($jenis_data === 'ltt') ? 'total_ltt' : 'total_ltp';
                
                $data = DB::table($table . ' as rh')
                    ->join('kabupaten as kab', 'kab.id', '=', 'rh.kabupaten_id')
                    ->whereYear('rh.tanggal', $tahun)
                    ->selectRaw("
                        kab.id          AS kabupaten_id,
                        kab.nama_kabupaten,
                        SUM(rh.{$totalField}) AS reguler,
                        SUM(rh.oplah) AS oplah,
                        SUM(rh.gogo) AS gogo,
                        SUM(rh.csr) AS csr,
                        SUM(rh.{$valField}) AS value
                    ")
                    ->groupBy('kab.id', 'kab.nama_kabupaten')
                    ->get();
                
                // Initialize all kabupatens
                Kabupaten::orderBy('id')->get()->each(function ($kab) use (&$result) {
                    $result[(string) $kab->id] = [
                        'nama_kabupaten' => $kab->nama_kabupaten,
                        'value'          => 0
                    ];
                });

                foreach ($data as $row) {
                    $result[(string) $row->kabupaten_id] = [
                        'nama_kabupaten' => $row->nama_kabupaten,
                        'value'          => (float) ($row->value ?? 0)
                    ];
                }
                return response()->json(['level' => 'kabupaten', 'data' => $result]);
            }

            $modelMap = [
                'ksa_tanam' => KsaLuasTanam::class,
                'ksa_panen' => KsaLuasPanen::class,
                'ksa_produksi' => KsaProduksi::class,
                'ip_padi' => IndeksPertanamanPadi::class,
                'lbs' => LuasBakuSawah::class,
            ];

            if (!isset($modelMap[$jenis_data])) {
                return response()->json(['level' => 'kabupaten', 'data' => []]);
            }

            $model = $modelMap[$jenis_data];
            $records = $model::where('tahun', $tahun)->get()->groupBy('kabupaten_id');

            Kabupaten::orderBy('id')->get()->each(function ($kab) use (&$result, $records, $jenis_data) {
                $kabRecords = $records->get($kab->id);
                $val = 0;
                if ($kabRecords) {
                    if ($jenis_data === 'ksa_tanam')    $val = $kabRecords->sum('luas_tanam');
                    elseif ($jenis_data === 'ksa_panen') $val = $kabRecords->sum('luas_panen');
                    elseif ($jenis_data === 'ksa_produksi') $val = $kabRecords->sum('produksi');
                    elseif ($jenis_data === 'ip_padi')  $val = $kabRecords->first()->ip; // IP is usually 1 record per year
                    elseif ($jenis_data === 'lbs')      $val = $kabRecords->first()->luas_baku_sawah; // LBS 1 record
                }
                
                $result[(string) $kab->id] = [
                    'nama_kabupaten' => $kab->nama_kabupaten,
                    'value'          => (float) $val
                ];
            });

            return response()->json(['level' => 'kabupaten', 'data' => $result]);
        }

        return response()->json(['level' => 'kabupaten', 'data' => []]);
    }


    // ─────────────────────────────────────────────────────────────
    /**
     * API: Data LTT & LTP per KECAMATAN dari rekap HARIAN
     * Sumber: rekap_harian_tanam & rekap_harian_panen
     * GET /api/map/kecamatan-data
     */
    public function kecamatanData()
    {
        $today = date('Y-m-d');

        // LTT per kecamatan — SUM(total_ltt) dari rekap_harian_tanam HARI INI
        $lttData = DB::table('rekap_harian_tanam as rht')
            ->join('kecamatan as kec', 'kec.id', '=', 'rht.kecamatan_id')
            ->join('kabupaten as kab', 'kab.id', '=', 'kec.kabupaten_id')
            ->where('rht.tanggal', $today)
            ->selectRaw('
                kec.id          AS kecamatan_id,
                kec.nama_kecamatan,
                kab.id          AS kabupaten_id,
                kab.nama_kabupaten,
                SUM(rht.total_ltt) AS total_ltt
            ')
            ->groupBy('kec.id', 'kec.nama_kecamatan', 'kab.id', 'kab.nama_kabupaten')
            ->get()
            ->keyBy('kecamatan_id');

        // LTP per kecamatan — SUM(total_ltp) dari rekap_harian_panen HARI INI
        $ltpData = DB::table('rekap_harian_panen as rhp')
            ->join('kecamatan as kec', 'kec.id', '=', 'rhp.kecamatan_id')
            ->join('kabupaten as kab', 'kab.id', '=', 'kec.kabupaten_id')
            ->where('rhp.tanggal', $today)
            ->selectRaw('
                kec.id          AS kecamatan_id,
                kec.nama_kecamatan,
                kab.id          AS kabupaten_id,
                kab.nama_kabupaten,
                SUM(rhp.total_ltp) AS total_ltp
            ')
            ->groupBy('kec.id', 'kec.nama_kecamatan', 'kab.id', 'kab.nama_kabupaten')
            ->get()
            ->keyBy('kecamatan_id');

        $result = [];

        // Gabung dari LTT
        foreach ($lttData as $kecId => $row) {
            $key          = $this->normalizeName($row->nama_kecamatan);
            $result[$key] = [
                'ltt'            => (float) ($row->total_ltt ?? 0),
                'ltp'            => (float) ($ltpData[$kecId]->total_ltp ?? 0),
                'tanggal'        => $today,
                'kabupaten_id'   => (string) $row->kabupaten_id,
                'nama_kabupaten' => $row->nama_kabupaten,
            ];
        }

        // Kecamatan yang hanya punya LTP
        foreach ($ltpData as $kecId => $row) {
            $key = $this->normalizeName($row->nama_kecamatan);
            if (!isset($result[$key])) {
                $result[$key] = [
                    'ltt'            => 0,
                    'ltp'            => (float) ($row->total_ltp ?? 0),
                    'tanggal'        => $today,
                    'kabupaten_id'   => (string) $row->kabupaten_id,
                    'nama_kabupaten' => $row->nama_kabupaten,
                ];
            }
        }

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────────
    /**
     * API: Data LTT, LTP, Produksi & LBS per KABUPATEN
     *
     * GET /api/map/kabupaten-data
     */
    public function kabupatenData()
    {
        $tahun = date('Y');

        // LTT per kabupaten dari rekap_bulanan_tanam
        $lttData = RekapBulananTanam::where('tahun', $tahun)
            ->get()->keyBy('kabupaten_id');

        // LTP per kabupaten dari rekap_bulanan_panen
        $ltpData = RekapBulananPanen::where('tahun', $tahun)
            ->get()->keyBy('kabupaten_id');
            
        // KSA Tanam per kabupaten
        $ksaTanamData = KsaLuasTanam::where('tahun', $tahun)
            ->selectRaw('kabupaten_id, SUM(luas_tanam) as total')
            ->groupBy('kabupaten_id')
            ->get()->keyBy('kabupaten_id');
            
        // KSA Panen per kabupaten
        $ksaPanenData = KsaLuasPanen::where('tahun', $tahun)
            ->selectRaw('kabupaten_id, SUM(luas_panen) as total')
            ->groupBy('kabupaten_id')
            ->get()->keyBy('kabupaten_id');
            
        // KSA Produksi per kabupaten
        $ksaProbData = KsaProduksi::where('tahun', $tahun)
            ->selectRaw('kabupaten_id, SUM(produksi) as total')
            ->groupBy('kabupaten_id')
            ->get()->keyBy('kabupaten_id');
            
        // Indeks Pertanaman per kabupaten
        $ipData = IndeksPertanamanPadi::where('tahun', $tahun)
            ->get()->keyBy('kabupaten_id');

        // LBS per kabupaten
        $lbsData = LuasBakuSawah::where('tahun', $tahun)
            ->get()->keyBy('kabupaten_id');

        $result = [];

        Kabupaten::orderBy('id')->get()->each(function ($kab) use (
            &$result, $tahun,
            $lttData, $ltpData, $ksaTanamData, $ksaPanenData, $ksaProbData, $ipData, $lbsData
        ) {
            $result[(string) $kab->id] = [
                'nama_kabupaten'  => $kab->nama_kabupaten,
                'ltt'             => (float) ($lttData[$kab->id]->total ?? 0),
                'ltp'             => (float) ($ltpData[$kab->id]->total ?? 0),
                'produksi'        => (float) ($produksiData[$kab->id]->total ?? 0),
                'ksa_tanam'       => (float) ($ksaTanamData[$kab->id]->total ?? 0),
                'ksa_panen'       => (float) ($ksaPanenData[$kab->id]->total ?? 0),
                'ksa_produksi'    => (float) ($ksaProbData[$kab->id]->total ?? 0),
                'ip'              => (float) ($ipData[$kab->id]->ip ?? 0),
                'lbs'             => (float) ($lbsData[$kab->id]->luas_baku_sawah ?? 0),
                'tahun'           => $tahun,
            ];
        });

        return response()->json($result);
    }

    // ─── Helper normalisasi nama ──────────────────────────────
    /**
     * Normalisasi nama — HARUS identik dengan normalizeName() di JavaScript
     */
    private function normalizeName(string $str): string
    {
        $str = strtoupper(trim($str));
        $str = preg_replace('/\s+/', ' ', $str);
        $str = preg_replace('/\bKEC\.?\s*/i', '', $str);
        $str = preg_replace('/\bKECAMATAN\s*/i', '', $str);
        $str = preg_replace('/\bKOTA\s+/i', '', $str);
        return trim($str);
    }
}