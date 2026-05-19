<?php

namespace App\Http\Controllers;

use App\Exports\RekapHarianPanenExport;
use Illuminate\Http\Request;
use App\Models\RekapHarianPanen;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RekapHarianPanenController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getAllowedKabupatens()
    {
        $user = Auth::user();

        if ($user->isKabupatenRestricted()) {
            return Kabupaten::where('id', $user->kabupaten_id)->get();
        }

        return Kabupaten::orderBy('id')->get();
    }

    private function resolveKabupatenId($requestKabupatenId = null): int
    {
        $user = Auth::user();

        if ($user->isKabupatenRestricted()) {
            return (int) $user->kabupaten_id;
        }

        return (int) $requestKabupatenId;
    }

    private function authorizeKabupaten(int $kabupatenId): void
    {
        if (!Auth::user()->canAccessKabupaten($kabupatenId)) {
            abort(403, 'Anda tidak memiliki akses ke data kabupaten ini.');
        }
    }

    // ── Index ─────────────────────────────────────────────────────────────────

        public function index(Request $request)
    {
        $dbYears = RekapHarianPanen::selectRaw('YEAR(tanggal) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (empty($dbYears)) {
            $dbYears = [date('Y')];
        }

        $tahun = (int) $request->get('tahun', $dbYears[0] ?? date('Y'));
        $years = $dbYears;

        $bulan = (int) $request->get('bulan', date('m'));
        $kabupatenId = $request->get('kabupaten_id');
        $kabupatens = Kabupaten::orderBy('id')->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $rekapsData = RekapHarianPanen::with(['kabupaten', 'kecamatan'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($kabupatenId, fn($q) => $q->where('kabupaten_id', $kabupatenId))
            ->get()
            ->keyBy('kecamatan_id');

        $allKecamatan = Kecamatan::with('kabupaten')
            ->when($kabupatenId, fn($q) => $q->where('kabupaten_id', $kabupatenId))
            ->orderBy('kabupaten_id')
            ->orderBy('nama_kecamatan')
            ->get();

        $rekaps = $allKecamatan->map(function ($kec) use ($rekapsData) {
            $row = $rekapsData->get($kec->id) ?? new RekapHarianPanen(['kabupaten_id' => $kec->kabupaten_id, 'kecamatan_id' => $kec->id]);
            $row->kabupaten_id = $kec->kabupaten_id; // paksa mengikuti kabupaten_id asli dari master kecamatan
            $row->id = $row->id ?? null;
            $row->setRelation('kabupaten', $kec->kabupaten);
            $row->setRelation('kecamatan', $kec);
            return $row;
        });

        $groupedRekaps = $rekaps->groupBy('kabupaten_id');

        $totals = $this->calculateTotals($rekapsData, $kabupatens);

        $grandTotals = $totals['grandTotals'];
        $kabTotalsData = $totals['kabTotalsData'];

        return view('rekap.harian.panen.index', compact(
            'groupedRekaps', 'tahun', 'bulan', 'years', 'months',
            'kabupatenId', 'kabupatens', 'grandTotals', 'kabTotalsData'
        ));
    }

        private function calculateTotals($rekapsData, $kabupatens)
    {
        /** @var array<string, float|int> $grandTotals */
        $grandTotals = ['target' => 0, 'total_panen' => 0, 'oplah' => 0, 'gogo' => 0, 'csr' => 0, 'total_ltp' => 0, 'realisasi' => 0];
        for ($d = 1; $d <= 31; $d++) $grandTotals["tgl_$d"] = 0;

        /** @var array<int, array> $kabTotalsData */
        $kabTotalsData = [];
        foreach ($kabupatens as $kab) {
            $kabTotalsData[$kab->id] = ['target' => 0, 'total_panen' => 0, 'oplah' => 0, 'gogo' => 0, 'csr' => 0, 'total_ltp' => 0, 'realisasi' => 0];
            for ($d = 1; $d <= 31; $d++) $kabTotalsData[$kab->id]["tgl_$d"] = 0;
        }

        foreach ($rekapsData as $row) {
            $kabId = $row->kabupaten_id;
            if (!isset($kabTotalsData[$kabId])) continue;

            $realisasi = $row->target - $row->total_ltp;

            $kabTotalsData[$kabId]['target'] += (float)$row->target;
            $kabTotalsData[$kabId]['total_panen'] += (float)$row->total_panen;
            $kabTotalsData[$kabId]['oplah'] += (float)$row->oplah;
            $kabTotalsData[$kabId]['gogo'] += (float)$row->gogo;
            $kabTotalsData[$kabId]['csr'] += (float)$row->csr;
            $kabTotalsData[$kabId]['total_ltp'] += (float)$row->total_ltp;
            $kabTotalsData[$kabId]['realisasi'] += (float)$realisasi;

            $grandTotals['target'] += (float)$row->target;
            $grandTotals['total_panen'] += (float)$row->total_panen;
            $grandTotals['oplah'] += (float)$row->oplah;
            $grandTotals['gogo'] += (float)$row->gogo;
            $grandTotals['csr'] += (float)$row->csr;
            $grandTotals['total_ltp'] += (float)$row->total_ltp;
            $grandTotals['realisasi'] += (float)$realisasi;

            for ($d = 1; $d <= 31; $d++) {
                $val = (float)($row->{"tgl_$d"} ?? 0);
                $kabTotalsData[$kabId]["tgl_$d"] += $val;
                $grandTotals["tgl_$d"] += $val;
            }
        }

        return ['grandTotals' => $grandTotals, 'kabTotalsData' => $kabTotalsData];
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $user = Auth::user();
        $kabupatens = $this->getAllowedKabupatens();

        $reqKabupatenId = $request->get('kabupaten_id');
        $reqKecamatanId = $request->get('kecamatan_id');
        $reqTahun = $request->get('tahun', date('Y'));
        $reqBulan = $request->get('bulan', date('m'));

        $kecamatans = $user->isKabupatenRestricted()
            ? Kecamatan::where('kabupaten_id', $user->kabupaten_id)
                ->orderBy('nama_kecamatan')->get()
            : collect();

        $defaultKabupatenId = $user->isKabupatenRestricted()
            ? (int) $user->kabupaten_id
            : null;

        $prefilledKecamatan = null;
        if ($reqKabupatenId && $reqKecamatanId) {
            $prefilledKecamatan = Kecamatan::with('kabupaten')
                ->where('id', $reqKecamatanId)
                ->where('kabupaten_id', $reqKabupatenId)
                ->first();
        }

        return view('rekap.harian.panen.create', compact(
            'kabupatens',
            'kecamatans',
            'defaultKabupatenId',
            'reqKabupatenId',
            'reqKecamatanId',
            'reqTahun',
            'reqBulan',
            'prefilledKecamatan'
        ));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'keterangan' => 'nullable|string|max:500',
            'oplah' => 'nullable|numeric|min:0',
            'gogo' => 'nullable|numeric|min:0',
            'csr' => 'nullable|numeric|min:0',
            'target' => 'nullable|numeric|min:0',
        ]);

        $tglRules = [];
        for ($d = 1; $d <= 31; $d++) {
            $tglRules["tgl_{$d}"] = 'nullable|numeric|min:0';
        }
        $request->validate($tglRules);

        $validated['kabupaten_id'] = $this->resolveKabupatenId($validated['kabupaten_id']);
        $this->authorizeKabupaten($validated['kabupaten_id']);

        $kecamatanValid = Kecamatan::where('id', $validated['kecamatan_id'])
            ->where('kabupaten_id', $validated['kabupaten_id'])
            ->exists();

        if (!$kecamatanValid) {
            return back()
                ->withInput()
                ->withErrors(['kecamatan_id' => 'Kecamatan tidak sesuai dengan kabupaten yang dipilih.']);
        }

        $tanggal = Carbon::create($validated['tahun'], $validated['bulan'], 1);

        $exists = RekapHarianPanen::where('kecamatan_id', $validated['kecamatan_id'])
            ->whereYear('tanggal', $validated['tahun'])
            ->whereMonth('tanggal', $validated['bulan'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['kecamatan_id' => 'Data untuk kecamatan, tahun, dan bulan ini sudah ada!']);
        }

        $totalPanen = 0;
        for ($d = 1; $d <= 31; $d++) {
            $totalPanen += $request->input("tgl_{$d}", 0);
        }

        $oplah = $request->input('oplah', 0);
        $gogo = $request->input('gogo', 0);
        $csr = $request->input('csr', 0);
        $totalLtp = $totalPanen + $oplah + $gogo + $csr;
        $target = $request->input('target', 0);
        $realisasi = $target - $totalLtp;

        $dataToInsert = [
            'kabupaten_id' => $validated['kabupaten_id'],
            'kecamatan_id' => $validated['kecamatan_id'],
            'tanggal' => $tanggal->format('Y-m-d'),
            'total_panen' => $totalPanen,
            'oplah' => $oplah,
            'gogo' => $gogo,
            'csr' => $csr,
            'total_ltp' => $totalLtp,
            'realisasi' => $realisasi,
            'target' => $target,
            'keterangan' => $validated['keterangan'],
            'created_by' => auth()->id(),
        ];

        for ($d = 1; $d <= 31; $d++) {
            $dataToInsert["tgl_{$d}"] = $request->input("tgl_{$d}", 0);
        }

        RekapHarianPanen::create($dataToInsert);

        return redirect()->route('rekap.harian.panen.index')
            ->with('success', 'Data harian berhasil disimpan! Total LTP: ' . number_format($totalLtp, 2) . ' Ha');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $rekap = RekapHarianPanen::with(['kabupaten', 'kecamatan'])->findOrFail($id);

        $this->authorizeKabupaten($rekap->kabupaten_id);

        $rekap->bulan = Carbon::parse($rekap->tanggal)->month;
        $rekap->tahun = Carbon::parse($rekap->tanggal)->year;

        $kabupatens = $this->getAllowedKabupatens();
        $kecamatans = Kecamatan::where('kabupaten_id', $rekap->kabupaten_id)
            ->orderBy('nama_kecamatan')->get();

        return view('rekap.harian.panen.edit', compact('rekap', 'kabupatens', 'kecamatans'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $rekap = RekapHarianPanen::findOrFail($id);

        $this->authorizeKabupaten($rekap->kabupaten_id);

        $validated = $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'keterangan' => 'nullable|string|max:500',
            'oplah' => 'nullable|numeric|min:0',
            'gogo' => 'nullable|numeric|min:0',
            'csr' => 'nullable|numeric|min:0',
            'target' => 'nullable|numeric|min:0',
        ]);

        $tglRules = [];
        for ($d = 1; $d <= 31; $d++) {
            $tglRules["tgl_{$d}"] = 'nullable|numeric|min:0';
        }
        $request->validate($tglRules);

        $validated['kabupaten_id'] = $this->resolveKabupatenId($validated['kabupaten_id']);
        $this->authorizeKabupaten($validated['kabupaten_id']);

        $kecamatanValid = Kecamatan::where('id', $validated['kecamatan_id'])
            ->where('kabupaten_id', $validated['kabupaten_id'])
            ->exists();

        if (!$kecamatanValid) {
            return back()
                ->withInput()
                ->withErrors(['kecamatan_id' => 'Kecamatan tidak sesuai dengan kabupaten yang dipilih.']);
        }

        $tanggal = Carbon::create($validated['tahun'], $validated['bulan'], 1);

        $exists = RekapHarianPanen::where('kecamatan_id', $validated['kecamatan_id'])
            ->whereYear('tanggal', $validated['tahun'])
            ->whereMonth('tanggal', $validated['bulan'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['kecamatan_id' => 'Data untuk kecamatan, tahun, dan bulan ini sudah ada!']);
        }

        $totalPanen = 0;
        for ($d = 1; $d <= 31; $d++) {
            $totalPanen += $request->input("tgl_{$d}", 0);
        }

        $oplah = $request->input('oplah', 0);
        $gogo = $request->input('gogo', 0);
        $csr = $request->input('csr', 0);
        $totalLtp = $totalPanen + $oplah + $gogo + $csr;
        $target = $request->input('target', 0);
        $realisasi = $target - $totalLtp;

        $dataToUpdate = [
            'kabupaten_id' => $validated['kabupaten_id'],
            'kecamatan_id' => $validated['kecamatan_id'],
            'tanggal' => $tanggal->format('Y-m-d'),
            'total_panen' => $totalPanen,
            'oplah' => $oplah,
            'gogo' => $gogo,
            'csr' => $csr,
            'total_ltp' => $totalLtp,
            'realisasi' => $realisasi,
            'target' => $target,
            'keterangan' => $validated['keterangan'],
            'updated_by' => auth()->id(),
        ];

        for ($d = 1; $d <= 31; $d++) {
            $dataToUpdate["tgl_{$d}"] = $request->input("tgl_{$d}", 0);
        }

        $rekap->update($dataToUpdate);

        return redirect()->route('rekap.harian.panen.index')
            ->with('success', 'Data berhasil diperbarui! Total LTP: ' . number_format($totalLtp, 2) . ' Ha');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $rekap = RekapHarianPanen::findOrFail($id);
        $this->authorizeKabupaten($rekap->kabupaten_id);
        $rekap->delete();

        if (str_contains(url()->previous(), '/edit')) {
            return redirect()->route('rekap.harian.panen.index')->with('success', 'Data berhasil dihapus!');
        }

        return back()->with('success', 'Data berhasil dihapus!');
    }

    // ── AJAX Kecamatan ────────────────────────────────────────────────────────

    public function getKecamatan(Request $request)
    {
        $user = Auth::user();

        $kabupatenId = $user->isKabupatenRestricted()
            ? $user->kabupaten_id
            : $request->kabupaten_id;

        return Kecamatan::where('kabupaten_id', $kabupatenId)
            ->orderBy('nama_kecamatan')
            ->get(['id', 'nama_kecamatan']);
    }

    // ── Export ────────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $export = new RekapHarianPanenExport($tahun, $bulan);

        return $export->download();
    }
}