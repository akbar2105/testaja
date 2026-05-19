<?php

namespace App\Http\Controllers;

use App\Models\KsaProduksi;
use App\Models\Kabupaten;
use App\Exports\KsaProduksiBulananExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * SANDING BULANAN PER TAHUN — CRUD
 * Pivot: baris = kabupaten, kolom = Jan s.d. Des, filter = tahun
 * Form input: array produksi[1..12] — satu submit untuk semua bulan sekaligus
 */
class KsaProduksiBulananController extends Controller
{
    // ── INDEX (Pivot tabel) ───────────────────────────────────────────────────

    public function index(Request $request)
    {
        $availableYears = KsaProduksi::getAvailableYears();
        $tahun       = (int) $request->get('tahun', $availableYears[0] ?? date('Y'));
        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;

        $user = auth()->user();
        $kabupatens = Kabupaten::orderBy('id')->get();
        $bulanList  = KsaProduksi::getBulanList();

        // Pivot: kabupaten_id → collection records (filter kabupaten jika dipilih)
        $query = KsaProduksi::where('tahun', $tahun);
        if ($kabupatenId) $query->where('kabupaten_id', $kabupatenId);
        $rows = $query->get()->groupBy('kabupaten_id');

        // Total per bulan (footer SUMATERA SELATAN)
        $totalQuery = KsaProduksi::where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(produksi) as total'));
        if ($kabupatenId) $totalQuery->where('kabupaten_id', $kabupatenId);
        $totalPerBulan = $totalQuery->groupBy('bulan')->pluck('total', 'bulan');

        // Kabupaten yang ditampilkan (filtered)
        $filteredKabupatens = $kabupatenId
            ? $kabupatens->where('id', $kabupatenId)->values()
            : $kabupatens;

        $totalDataRecords = $rows->flatten()->count();
        
        $sumTotal     = $totalPerBulan->sum();
        $maxBulanVal  = $totalPerBulan->max();
        $maxBulanKey  = $totalPerBulan->search($maxBulanVal);
        $maxBulanNama = $maxBulanKey !== false ? ($bulanList[$maxBulanKey] ?? '-') : '-';

        return view('ksa.produksi.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $user = auth()->user();
        $kabupatens = $user->isKabupatenRestricted() ? Kabupaten::where('id', $user->kabupaten_id)->get() : Kabupaten::orderBy('id')->get();
        $bulanList     = KsaProduksi::getBulanList();
        $selectedKabId = $user->isKabupatenRestricted() ? $user->kabupaten_id : $request->get('kabupaten_id');
        $selectedTahun = (int) $request->get('tahun', date('Y'));

        return view('ksa.produksi.bulanan.create', compact(
            'kabupatens', 'bulanList', 'selectedKabId', 'selectedTahun'
        ));
    }

    // ── STORE (array produksi[1..12]) ─────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'tahun'        => 'required|integer|min:2000|max:2100',
            'produksi'     => 'required|array',
            'produksi.*'   => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);
        if (!auth()->user()->canAccessKabupaten($request->kabupaten_id)) { abort(403, 'Akses ditolak.'); }


        $filled = collect($request->produksi)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['produksi' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $kabupatenId = $request->kabupaten_id;
        $tahun       = $request->tahun;
        $keterangan  = $request->keterangan;
        $saved = $skipped = 0;

        foreach ($filled as $bulan => $nilai) {
            $bulan = (int) $bulan;
            if ($bulan < 1 || $bulan > 12) continue;

            $exists = KsaProduksi::where([
                'kabupaten_id' => $kabupatenId,
                'bulan'        => $bulan,
                'tahun'        => $tahun,
            ])->exists();

            if ($exists) { $skipped++; continue; }

            KsaProduksi::create([
                'kabupaten_id' => $kabupatenId,
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'produksi'     => $nilai,
                'keterangan'   => $keterangan,
            ]);
            $saved++;
        }

        $msg = "Berhasil menyimpan {$saved} bulan data KSA Produksi tahun {$tahun}.";
        if ($skipped > 0) $msg .= " {$skipped} bulan dilewati karena sudah ada data (gunakan Edit untuk mengubah).";

        return redirect()->route('ksa.produksi.bulanan.index', ['tahun' => $tahun])->with('success', $msg);
    }

    // ── EDIT (bulk: semua bulan untuk 1 kabupaten + tahun) ───────────────────

    public function edit(Request $request, string|int $kabupatenId)
    {
        $kabupaten      = Kabupaten::findOrFail($kabupatenId);
        $tahun          = (int) $request->get('tahun', date('Y'));
        $availableYears = array_unique(array_merge(KsaProduksi::getAvailableYears(), [date('Y')]));
        rsort($availableYears);

        $existingData = KsaProduksi::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('bulan');

        return view('ksa.produksi.bulanan.edit', compact(
            'kabupaten', 'tahun', 'availableYears', 'existingData'
        ));
    }

    // ── UPDATE BULK ───────────────────────────────────────────────────────────

    public function update(Request $request, string|int $kabupatenId)
    {
        $request->validate([
            'tahun'      => 'required|integer|min:2000|max:2100',
            'produksi'   => 'required|array',
            'produksi.*' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $filled = collect($request->produksi)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['produksi' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $tahun      = $request->tahun;
        $keterangan = $request->keterangan;

        // Hapus bulan yang dikosongkan
        $emptyBulans = collect($request->produksi)
            ->filter(fn($v) => $v === null || $v === '')
            ->keys()->map(fn($b) => (int) $b);

        if ($emptyBulans->isNotEmpty()) {
            KsaProduksi::where('kabupaten_id', $kabupatenId)
                ->where('tahun', $tahun)
                ->whereIn('bulan', $emptyBulans)
                ->delete();
        }

        // Upsert bulan yang diisi
        foreach ($filled as $bulan => $nilai) {
            $bulan = (int) $bulan;
            if ($bulan < 1 || $bulan > 12) continue;
            KsaProduksi::updateOrCreate(
                ['kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $tahun],
                ['produksi' => $nilai, 'keterangan' => $keterangan]
            );
        }

        return redirect()->route('ksa.produksi.bulanan.index', ['tahun' => $tahun])
            ->with('success', 'Data KSA Produksi berhasil diperbarui!');
    }

    // ── DESTROY BULK ─────────────────────────────────────────────────────────

    public function destroy(Request $request, string|int $kabupatenId)
    {
        $tahun = (int) $request->get('tahun', date('Y'));

        KsaProduksi::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $tahun)
            ->delete();

        return redirect()->route('ksa.produksi.bulanan.index', ['tahun' => $tahun])
            ->with('success', "Semua data produksi tahun {$tahun} untuk kabupaten ini berhasil dihapus!");
    }

    // ── EXPORT ────────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(new KsaProduksiBulananExport($tahun), "ksa-produksi-bulanan-{$tahun}.xlsx");
    }
}