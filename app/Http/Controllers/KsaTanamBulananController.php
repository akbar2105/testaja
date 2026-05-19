<?php

namespace App\Http\Controllers;

use App\Models\KsaLuasTanam;
use App\Models\Kabupaten;
use App\Exports\KsaTanamBulananExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KsaTanamBulananController extends Controller
{
    private function getFilterYears(): array
    {
        return KsaLuasTanam::getAvailableYears();
    }

    public function index(Request $request)
    {
        $availableYears = $this->getFilterYears(); // hanya tahun yang ada data
        $tahun          = (int) $request->get('tahun', $availableYears[0] ?? date('Y'));
        $kabupatenId    = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;

        $user = auth()->user();
        $kabupatens = Kabupaten::orderBy('id')->get();
        $bulanList  = KsaLuasTanam::getBulanList();

        $query = KsaLuasTanam::where(function ($q) use ($tahun) {
            $q->where(function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun)->whereIn('bulan', [10, 11, 12]);
            })->orWhere(function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun + 1)->whereBetween('bulan', [1, 9]);
            });
        });
        if ($kabupatenId) $query->where('kabupaten_id', $kabupatenId);
        $rows = $query->get()->groupBy('kabupaten_id');

        $totalQuery = KsaLuasTanam::where(function ($q) use ($tahun) {
            $q->where(function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun)->whereIn('bulan', [10, 11, 12]);
            })->orWhere(function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun + 1)->whereBetween('bulan', [1, 9]);
            });
        })->select('bulan', DB::raw('SUM(luas_tanam) as total'));
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

        return view('ksa.tanam.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }
    
    public function create(Request $request)
    {
        $user = auth()->user();
        $kabupatens = $user->isKabupatenRestricted() ? Kabupaten::where('id', $user->kabupaten_id)->get() : Kabupaten::orderBy('id')->get();
        $bulanList     = KsaLuasTanam::getBulanList();
        $selectedKabId = $user->isKabupatenRestricted() ? $user->kabupaten_id : $request->get('kabupaten_id');
        $selectedTahun = (int) $request->get('tahun', date('Y'));

        return view('ksa.tanam.bulanan.create', compact(
            'kabupatens', 'bulanList', 'selectedKabId', 'selectedTahun'
        ));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'tahun'        => 'required|integer|min:2000|max:2100',
            'luas_tanam'   => 'required|array',
            'luas_tanam.*' => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);
        if (!auth()->user()->canAccessKabupaten($request->kabupaten_id)) { abort(403, 'Akses ditolak.'); }


        $filled = collect($request->luas_tanam)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['luas_tanam' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $kabupatenId = $request->kabupaten_id;
        $tahun       = $request->tahun;
        $saved = $skipped = 0;

        foreach ($filled as $bulan => $nilai) {
            $bulan   = (int) $bulan;
            $dbTahun = in_array($bulan, [10, 11, 12]) ? $tahun : $tahun + 1;
            if (KsaLuasTanam::where(['kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $dbTahun])->exists()) {
                $skipped++; continue;
            }
            KsaLuasTanam::create([
                'kabupaten_id' => $kabupatenId,
                'bulan'        => $bulan,
                'tahun'        => $dbTahun,
                'luas_tanam'   => $nilai,
                'keterangan'   => $request->keterangan,
            ]);
            $saved++;
        }

        $msg = "Berhasil menyimpan {$saved} bulan data KSA Tanam periode Okt {$tahun}–Sep " . ($tahun + 1) . ".";
        if ($skipped > 0) $msg .= " {$skipped} bulan dilewati (sudah ada data).";
        return redirect()->route('ksa.tanam.bulanan.index', ['tahun' => $tahun])->with('success', $msg);
    }

    // ── EDIT ──────────────────────────────────────────────────────────────────

    public function edit(Request $request, string|int $kabupatenId)
    {
        $kabupaten = Kabupaten::findOrFail($kabupatenId);
        $tahun     = (int) $request->get('tahun', date('Y'));

        $existingData = KsaLuasTanam::where('kabupaten_id', $kabupatenId)
            ->where(function ($q) use ($tahun) {
                $q->where(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun)->whereIn('bulan', [10, 11, 12]);
                })->orWhere(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun + 1)->whereBetween('bulan', [1, 9]);
                });
            })
            ->get()->keyBy('bulan');

        return view('ksa.tanam.bulanan.edit', compact('kabupaten', 'tahun', 'existingData'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────

    public function update(Request $request, string|int $kabupatenId)
    {
        $request->validate([
            'tahun'        => 'required|integer|min:2000|max:2100',
            'luas_tanam'   => 'required|array',
            'luas_tanam.*' => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:500',
        ]);

        $filled = collect($request->luas_tanam)->filter(fn($v) => $v !== null && $v !== '');
        if ($filled->isEmpty()) {
            return back()->withErrors(['luas_tanam' => 'Minimal satu bulan harus diisi.'])->withInput();
        }

        $tahun = $request->tahun;
        $emptyBulans = collect($request->luas_tanam)->filter(fn($v) => $v === null || $v === '')->keys();
        foreach ($emptyBulans as $bulan) {
            $bulan   = (int) $bulan;
            $dbTahun = in_array($bulan, [10, 11, 12]) ? $tahun : $tahun + 1;
            KsaLuasTanam::where('kabupaten_id', $kabupatenId)
                ->where('bulan', $bulan)->where('tahun', $dbTahun)->delete();
        }
        foreach ($filled as $bulan => $nilai) {
            $bulan   = (int) $bulan;
            $dbTahun = in_array($bulan, [10, 11, 12]) ? $tahun : $tahun + 1;
            KsaLuasTanam::updateOrCreate(
                ['kabupaten_id' => $kabupatenId, 'bulan' => $bulan, 'tahun' => $dbTahun],
                ['luas_tanam' => $nilai, 'keterangan' => $request->keterangan]
            );
        }
        return redirect()->route('ksa.tanam.bulanan.index', ['tahun' => $tahun])
            ->with('success', 'Data KSA Tanam berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────

    public function destroy(Request $request, string|int $kabupatenId)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        KsaLuasTanam::where('kabupaten_id', $kabupatenId)
            ->where(function ($q) use ($tahun) {
                $q->where(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun)->whereIn('bulan', [10, 11, 12]);
                })->orWhere(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun + 1)->whereBetween('bulan', [1, 9]);
                });
            })->delete();
        return redirect()->route('ksa.tanam.bulanan.index', ['tahun' => $tahun])
            ->with('success', "Data tanam periode Okt {$tahun}–Sep " . ($tahun + 1) . " berhasil dihapus!");
    }

    // ── EXPORT ────────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(
            new KsaTanamBulananExport($tahun),
            "ksa-tanam-bulanan-okt{$tahun}-sep" . ($tahun + 1) . ".xlsx"
        );
    }
}