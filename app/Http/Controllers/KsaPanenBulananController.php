<?php

namespace App\Http\Controllers;

use App\Models\KsaLuasPanen;
use App\Models\Kabupaten;
use App\Exports\KsaPanenBulananExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KsaPanenBulananController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = KsaLuasPanen::getAvailableYears();
        $tahun       = (int) $request->get('tahun', $availableYears[0] ?? date('Y'));
        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;

        $user = auth()->user();
        $kabupatens = Kabupaten::orderBy('id')->get();
        $bulanList  = KsaLuasPanen::getBulanList();

        $query = KsaLuasPanen::where('tahun', $tahun);
        if ($kabupatenId) $query->where('kabupaten_id', $kabupatenId);
        $rows = $query->get()->groupBy('kabupaten_id');

        $totalQuery = KsaLuasPanen::where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(luas_panen) as total'));
        if ($kabupatenId) $totalQuery->where('kabupaten_id', $kabupatenId);
        $totalPerBulan = $totalQuery->groupBy('bulan')->pluck('total', 'bulan');

        $filteredKabupatens = $kabupatenId
            ? $kabupatens->where('id', $kabupatenId)->values()
            : $kabupatens;

        $totalDataRecords = $rows->flatten()->count();
        $sumTotal     = $totalPerBulan->sum();
        $maxBulanVal  = $totalPerBulan->max();
        $maxBulanKey  = $totalPerBulan->search($maxBulanVal);
        $maxBulanNama = $maxBulanKey !== false ? ($bulanList[$maxBulanKey] ?? '-') : '-';

        return view('ksa.panen.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $kabupatens     = $user->isKabupatenRestricted() ? Kabupaten::where('id', $user->kabupaten_id)->get() : Kabupaten::orderBy('id')->get();
        $bulanList      = KsaLuasPanen::getBulanList();
        $availableYears = array_unique(array_merge(KsaLuasPanen::getAvailableYears(), [date('Y')]));
        rsort($availableYears);
        $selectedKabId = $user->isKabupatenRestricted() ? $user->kabupaten_id : $request->get('kabupaten_id');
        $selectedTahun = (int) $request->get('tahun', date('Y'));

        return view('ksa.panen.bulanan.create', compact(
            'kabupatens', 'bulanList', 'availableYears', 'selectedKabId', 'selectedTahun'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'tahun'        => 'required|integer|min:2000|max:2100',
            'luas_panen'   => 'required|array',
            'luas_panen.*' => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);
        if (!auth()->user()->canAccessKabupaten($request->kabupaten_id)) { abort(403, 'Akses ditolak.'); }


        $filled = collect($request->luas_panen)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['luas_panen' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $kabupatenId = $request->kabupaten_id;
        $tahun       = $request->tahun;
        $saved = $skipped = 0;

        foreach ($filled as $bulan => $nilai) {
            $bulan = (int) $bulan;
            if ($bulan < 1 || $bulan > 12) continue;
            if (KsaLuasPanen::where(['kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $tahun])->exists()) {
                $skipped++; continue;
            }
            KsaLuasPanen::create([
                'kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $tahun,
                'luas_panen'   => $nilai, 'keterangan' => $request->keterangan,
            ]);
            $saved++;
        }

        $msg = "Berhasil menyimpan {$saved} bulan data KSA Panen tahun {$tahun}.";
        if ($skipped > 0) $msg .= " {$skipped} bulan dilewati (sudah ada data).";
        return redirect()->route('ksa.panen.bulanan.index', ['tahun' => $tahun])->with('success', $msg);
    }

    public function edit(Request $request, string|int $kabupatenId)
    {
        if (!auth()->user()->canAccessKabupaten($kabupatenId)) { abort(403, 'Akses ditolak.'); }

        $kabupaten      = Kabupaten::findOrFail($kabupatenId);
        $tahun          = (int) $request->get('tahun', date('Y'));
        $availableYears = array_unique(array_merge(KsaLuasPanen::getAvailableYears(), [date('Y')]));
        rsort($availableYears);
        $existingData = KsaLuasPanen::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $tahun)->get()->keyBy('bulan');

        return view('ksa.panen.bulanan.edit', compact('kabupaten', 'tahun', 'availableYears', 'existingData'));
    }

    public function update(Request $request, string|int $kabupatenId)
    {
        if (!auth()->user()->canAccessKabupaten($kabupatenId)) { abort(403, 'Akses ditolak.'); }

        $request->validate([
            'tahun'        => 'required|integer|min:2000|max:2100',
            'luas_panen'   => 'required|array',
            'luas_panen.*' => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        $filled = collect($request->luas_panen)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['luas_panen' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $tahun = $request->tahun;
        $emptyBulans = collect($request->luas_panen)->filter(fn($v) => $v === null || $v === '')->keys()->map(fn($b) => (int) $b);
        if ($emptyBulans->isNotEmpty()) {
            KsaLuasPanen::where('kabupaten_id', $kabupatenId)->where('tahun', $tahun)->whereIn('bulan', $emptyBulans)->delete();
        }
        foreach ($filled as $bulan => $nilai) {
            $bulan = (int) $bulan;
            if ($bulan < 1 || $bulan > 12) continue;
            KsaLuasPanen::updateOrCreate(
                ['kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $tahun],
                ['luas_panen' => $nilai, 'keterangan' => $request->keterangan]
            );
        }
        return redirect()->route('ksa.panen.bulanan.index', ['tahun' => $tahun])->with('success', 'Data KSA Panen berhasil diperbarui!');
    }

    public function destroy(Request $request, string|int $kabupatenId)
    {
        if (!auth()->user()->canAccessKabupaten($kabupatenId)) { abort(403, 'Akses ditolak.'); }

        $tahun = (int) $request->get('tahun', date('Y'));
        KsaLuasPanen::where('kabupaten_id', $kabupatenId)->where('tahun', $tahun)->delete();
        return redirect()->route('ksa.panen.bulanan.index', ['tahun' => $tahun])->with('success', "Data panen tahun {$tahun} berhasil dihapus!");
    }

    public function destroySingle(Request $request, int $id)
    {
        $rec = KsaLuasPanen::findOrFail($id);
        $tahun = $rec->tahun;
        $rec->delete();
        return redirect()->route('ksa.panen.bulanan.index', ['tahun' => $tahun])->with('success', 'Data berhasil dihapus!');
    }

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(new KsaPanenBulananExport($tahun), "ksa-panen-bulanan-{$tahun}.xlsx");
    }
}