<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\KsaLuasTanam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GrafikKsaTanamController extends Controller
{
            private array $chartColors = [
        '#F97316','#F59E0B','#EF4444','#10B981',
        '#3B82F6','#8B5CF6','#EC4899','#14B8A6',
        '#06B6D4','#84CC16','#F43F5E','#A855F7',
        '#0EA5E9','#22D3EE','#FB923C','#4ADE80',
    ];

    private array $bulanSingkat = [
        10 => 'Okt', 11 => 'Nov', 12 => 'Des', 1 => 'Jan',  
        2 => 'Feb',  3 => 'Mar', 4 => 'Apr',  5 => 'Mei',  
        6 => 'Jun', 7 => 'Jul',  8 => 'Agt',  9 => 'Sep',
    ];

    public function index(Request $request)
    {
        $tab           = $request->input('tab', 'bulanan');
        $tahunTersedia = KsaLuasTanam::distinct()->orderBy('tahun','desc')->pluck('tahun');
        $kabupatens    = Kabupaten::orderBy('id')->get();

        $dataBulanan  = [];
        $tahunBulanan = null;
        $totalBulanan = array_fill(0, 12, 0.0);

        $dataTahunan  = [];
        $tahunAwalT   = null;
        $tahunAkhirT  = null;
        $totalTahunan = [];
        $tahunRangeT  = [];

        if ($tab === 'bulanan') {
            $tahunBulanan = (int) $request->input('tahun_bulanan', $tahunTersedia->first() ?? date('Y'));
            $recordsBulanan = KsaLuasTanam::whereIn('tahun', [$tahunBulanan, $tahunBulanan + 1])->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten, 'data' => []];
                $slotIdx = 0;
                $kabRecords = $recordsBulanan->get($kab->id, collect());
                foreach ($this->bulanSingkat as $bulanNum => $label) {
                    $dbTahun = in_array($bulanNum, [10, 11, 12]) ? $tahunBulanan : $tahunBulanan + 1;
                    $rec     = $kabRecords->where('tahun', $dbTahun)->firstWhere('bulan', $bulanNum);
                    $val = $rec ? (float)($rec->luas_tanam ?? 0) : 0.0;
                    $row['data'][]          = $val;
                    $totalBulanan[$slotIdx] += $val;
                    $slotIdx++;
                }
                $dataBulanan[] = $row;
            }
        }

        if ($tab === 'tahunan') {
            $tahunAwalT  = (int) $request->input('tahun_awal_tahunan',  $tahunTersedia->min() ?? date('Y') - 4);
            $tahunAkhirT = (int) $request->input('tahun_akhir_tahunan', $tahunTersedia->max() ?? date('Y'));
            $tahunRangeT = range($tahunAwalT, $tahunAkhirT);
            foreach ($tahunRangeT as $t) $totalTahunan[$t] = 0.0;

            $dbYears = range($tahunAwalT, $tahunAkhirT + 1);
            $recordsTahunan = KsaLuasTanam::whereIn('tahun', $dbYears)->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $kabRecords = $recordsTahunan->get($kab->id, collect());
                foreach ($tahunRangeT as $periodTahun) {
                    $v = (float) $kabRecords->filter(function($r) use ($periodTahun) {
                        if ($r->tahun == $periodTahun && in_array($r->bulan, [10, 11, 12])) return true;
                        if ($r->tahun == $periodTahun + 1 && $r->bulan >= 1 && $r->bulan <= 9) return true;
                        return false;
                    })->sum('luas_tanam');

                    $row[$periodTahun]           = $v;
                    $totalTahunan[$periodTahun] += $v;
                }
                $dataTahunan[] = $row;
            }
        }

        $chartColors      = $this->chartColors;
        $chartColorsCount = count($this->chartColors);
        $bulanLabels      = array_values($this->bulanSingkat);
        $tahunRangeT      = $tahunRangeT ?? [];

        return view('user.grafik.ksa.tanam', compact('tab', 'kabupatens', 'tahunTersedia', 'chartColors', 'chartColorsCount', 'bulanLabels',
            'dataBulanan', 'tahunBulanan', 'totalBulanan',
            'dataTahunan', 'tahunAwalT', 'tahunAkhirT', 'tahunRangeT', 'totalTahunan'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tab       = $request->input('tab', 'bulanan');
        $chartMode = $request->input('chart_mode', 'stacked');
        if (isset($tab) && $tab === 'tahunan') {
            $chartMode = 'grouped';
        }

        $kabupatens = Kabupaten::orderBy('id')->get();

        $chartColors  = $this->chartColors;
        $headerColor1 = '#F97316';
        $headerColor2 = '#F59E0B';
        $sectionBg    = '#FEF3C7';
        $sectionColor = '#92400E';
        $useDecimals  = false;
        $tanggalCetak = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
                      . ' pukul ' . Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';

        if ($tab === 'bulanan') {
            $tahunBulanan  = (int) $request->input('tahun_bulanan', KsaLuasTanam::max('tahun') ?? date('Y'));
            $kolom         = array_values($this->bulanSingkat);
            $totalPerKolom = array_fill_keys($kolom, 0.0);
            $kolomKey      = null;
            $data          = [];

            $recordsBulanan = KsaLuasTanam::whereIn('tahun', [$tahunBulanan, $tahunBulanan + 1])->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $slotIdx = 0;
                $kabRecords = $recordsBulanan->get($kab->id, collect());
                foreach ($this->bulanSingkat as $bulanNum => $label) {
                    $dbTahun = in_array($bulanNum, [10, 11, 12]) ? $tahunBulanan : $tahunBulanan + 1;
                    $rec     = $kabRecords->where('tahun', $dbTahun)->firstWhere('bulan', $bulanNum);
                    $val = $rec ? (float)($rec->luas_tanam ?? 0) : 0.0;
                    $row[$label]           = $val;
                    $totalPerKolom[$label] += $val;
                    $slotIdx++;
                }
                $data[] = $row;
            }

            $pdfMode      = 'bulanan';
            $periodeLabel = 'Okt ' . $tahunBulanan . ' – Sep ' . ($tahunBulanan + 1);
            $judulPdf     = 'KSA — Luas Tanam per Bulan';
            $judulTabel   = 'Rekapitulasi KSA Luas Tanam (Ha) — ' . $periodeLabel;

        } else {
            $tahunAwalT  = (int) $request->input('tahun_awal_tahunan',  KsaLuasTanam::min('tahun') ?? date('Y') - 4);
            $tahunAkhirT = (int) $request->input('tahun_akhir_tahunan', KsaLuasTanam::max('tahun') ?? date('Y'));
            $tr          = range($tahunAwalT, $tahunAkhirT);
            $kolom       = array_map(fn($t) => 'Okt ' . $t, $tr);
            $kolomKey    = $tr;
            $totalPerKolom = array_fill_keys($tr, 0.0);
            $data          = [];

            $dbYears = range($tahunAwalT, $tahunAkhirT + 1);
            $recordsTahunan = KsaLuasTanam::whereIn('tahun', $dbYears)->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $kabRecords = $recordsTahunan->get($kab->id, collect());
                foreach ($tr as $periodTahun) {
                    $v = (float) $kabRecords->filter(function($r) use ($periodTahun) {
                        if ($r->tahun == $periodTahun && in_array($r->bulan, [10, 11, 12])) return true;
                        if ($r->tahun == $periodTahun + 1 && $r->bulan >= 1 && $r->bulan <= 9) return true;
                        return false;
                    })->sum('luas_tanam');
                    $row[$periodTahun]            = $v;
                    $totalPerKolom[$periodTahun] += $v;
                }
                $data[] = $row;
            }

            $pdfMode      = 'tahunan';
            $periodeLabel = $tahunAwalT . '–' . $tahunAkhirT;
            $judulPdf     = 'KSA — Total Luas Tanam per Tahun';
            $judulTabel   = 'Rekapitulasi KSA Total Luas Tanam (Ha) — ' . $periodeLabel;
        }

        $metrics = \App\Services\PdfGraphicService::prepareData($data, $kolom, $pdfMode ?? 'bulanan', $judulPdf, $chartColors, $kolomKey ?? null, $useDecimals ?? false, $headerColor1 ?? '#111827', $headerColor2 ?? null, $chartMode ?? null);
        $pdf = Pdf::loadView('user.grafik.pdf.grafik-pdf', compact('metrics', 'data', 'kolom', 'kolomKey', 'totalPerKolom', 'chartColors',
            'judulPdf', 'judulTabel', 'periodeLabel', 'tanggalCetak',
            'headerColor1', 'headerColor2', 'sectionBg', 'sectionColor',
            'useDecimals', 'pdfMode', 'chartMode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('ksa-tanam-' . $periodeLabel . '.pdf');
    }
}