<?php

namespace App\Http\Controllers;

use App\Models\LuasBakuSawah;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LbsController extends Controller
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

    private function resolveKabupatenId($requestKabupatenId = null): ?int
    {
        $user = Auth::user();

        if ($user->isKabupatenRestricted()) {
            return (int) $user->kabupaten_id;
        }

        return $requestKabupatenId ? (int) $requestKabupatenId : null;
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
        $user        = Auth::user();
        $search      = $request->get('search');

        $availableYears  = LuasBakuSawah::getAvailableYears();
        $defaultYears    = array_slice($availableYears, 0, 1);
        $selectedYears   = $request->get('tahun', $defaultYears);

        if (!is_array($selectedYears)) {
            $selectedYears = [$selectedYears];
        }
        sort($selectedYears);

        // Gunakan parameter url murni (tanpa paksaan kabupaten_id user jika tidak request filter)
        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $kabupatens  = Kabupaten::orderBy('id')->get();
        $groupedData = [];
        
        $records = LuasBakuSawah::whereIn('tahun', $selectedYears);
        if ($kabupatenId) {
            $records->where('kabupaten_id', $kabupatenId);
        }
        $records = $records->get()->groupBy('kabupaten_id');

        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;
            if ($search && stripos($kab->nama_kabupaten, $search) === false) continue;

            $kabData = ['kabupaten' => $kab, 'years' => [], 'records' => [], 'row_total' => 0];
            
            $kabRecords = $records->get($kab->id, collect());

            foreach ($selectedYears as $year) {
                $lbs = $kabRecords->firstWhere('tahun', $year);
                $val = $lbs ? (float) $lbs->luas_baku_sawah : 0;
                $kabData['years'][$year] = $val;
                $kabData['records'][$year] = $lbs;
                $kabData['row_total'] += $val;
            }

            $groupedData[] = $kabData;
        }

        $totals = [];
        $sumTotalAll = 0;
        foreach ($selectedYears as $year) {
            $query = LuasBakuSawah::where('tahun', $year);
            if ($kabupatenId) {
                $query->where('kabupaten_id', $kabupatenId);
            }
            $val = (float) $query->sum('luas_baku_sawah');
            $totals[$year] = $val;
            $sumTotalAll += $val;
        }

        return view('lbs.index', compact(
            'groupedData', 'totals', 'selectedYears',
            'kabupatenId', 'search', 'kabupatens', 'availableYears', 'sumTotalAll'
        ));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        $user       = Auth::user();
        $kabupatens = $this->getAllowedKabupatens();
        $years      = range(date('Y') + 1, date('Y') - 20);

        $defaultKabupatenId = $user->isKabupatenRestricted()
            ? (int) $user->kabupaten_id
            : null;

        return view('lbs.create', compact('kabupatens', 'years', 'defaultKabupatenId'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kabupaten_id'    => 'required|exists:kabupaten,id',
            'tahun'           => 'required|integer|min:2000|max:2100',
            'luas_baku_sawah' => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string|max:1000',
        ]);

        // Paksa kabupaten_id sesuai user — abaikan nilai dari form jika dibatasi
        $validated['kabupaten_id'] = $this->resolveKabupatenId($validated['kabupaten_id']);

        $this->authorizeKabupaten($validated['kabupaten_id']);

        $exists = LuasBakuSawah::where('kabupaten_id', $validated['kabupaten_id'])
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['error' => 'Data LBS untuk kabupaten dan tahun ini sudah ada!'])
                ->withInput();
        }

        LuasBakuSawah::create($validated);

        return redirect()->route('lbs.index', ['tahun[]' => $validated['tahun']])
            ->with('success', 'Data Luas Baku Sawah berhasil ditambahkan!');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $lbs = LuasBakuSawah::with('kabupaten')->findOrFail($id);

        $this->authorizeKabupaten($lbs->kabupaten_id);

        $otherYears = LuasBakuSawah::where('kabupaten_id', $lbs->kabupaten_id)
            ->where('tahun', '!=', $lbs->tahun)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('lbs.show', compact('lbs', 'otherYears'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $lbs = LuasBakuSawah::findOrFail($id);

        $this->authorizeKabupaten($lbs->kabupaten_id);

        $kabupatens = $this->getAllowedKabupatens();
        $years      = range(date('Y') + 1, date('Y') - 20);

        return view('lbs.edit', compact('lbs', 'kabupatens', 'years'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $lbs = LuasBakuSawah::findOrFail($id);

        $this->authorizeKabupaten($lbs->kabupaten_id);

        $validated = $request->validate([
            'kabupaten_id'    => 'required|exists:kabupaten,id',
            'tahun'           => 'required|integer|min:2000|max:2100',
            'luas_baku_sawah' => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string|max:1000',
        ]);

        $validated['kabupaten_id'] = $this->resolveKabupatenId($validated['kabupaten_id']);

        $this->authorizeKabupaten($validated['kabupaten_id']);

        $exists = LuasBakuSawah::where('kabupaten_id', $validated['kabupaten_id'])
            ->where('tahun', $validated['tahun'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['error' => 'Data LBS untuk kabupaten dan tahun ini sudah ada!'])
                ->withInput();
        }

        $lbs->update($validated);

        return redirect()->route('lbs.index', ['tahun[]' => $validated['tahun']])
            ->with('success', 'Data Luas Baku Sawah berhasil diperbarui!');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $lbs = LuasBakuSawah::findOrFail($id);

        $this->authorizeKabupaten($lbs->kabupaten_id);

        $tahun = $lbs->tahun;
        $lbs->delete();

        return redirect()->route('lbs.index', ['tahun[]' => $tahun])
            ->with('success', 'Data Luas Baku Sawah berhasil dihapus!');
    }

    // ── Export ────────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $selectedYears = $request->get('tahun', []);

        if (!is_array($selectedYears)) {
            $selectedYears = [$selectedYears];
        }
        sort($selectedYears);

        $yearCount = count($selectedYears);

        if ($yearCount == 1) {
            $filenameSuffix = $selectedYears[0];
        } elseif ($yearCount == 2) {
            $filenameSuffix = $selectedYears[0] . '-dan-' . $selectedYears[1];
        } elseif ($yearCount == 3) {
            $filenameSuffix = $selectedYears[0] . '-' . $selectedYears[1] . '-dan-' . $selectedYears[2];
        } else {
            $filenameSuffix = min($selectedYears) . '-sampai-' . max($selectedYears);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LuasBakuSawahExport($selectedYears),
            'sanding-luas-baku-sawah-' . $filenameSuffix . '.xlsx'
        );
    }
}