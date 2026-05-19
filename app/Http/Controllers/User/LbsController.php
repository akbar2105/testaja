<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LuasBakuSawah;
use App\Models\Kabupaten;
use Illuminate\Http\Request;

class LbsController extends Controller
{
    public function index(Request $request)
    {
        $search      = $request->get('search');

        $availableYears  = LuasBakuSawah::getAvailableYears();
        $defaultYears    = array_slice($availableYears, 0, 1);
        $selectedYears   = $request->get('tahun', $defaultYears);

        if (!is_array($selectedYears)) {
            $selectedYears = [$selectedYears];
        }
        sort($selectedYears);

        $kabupatenId = $request->get('kabupaten_id');
        $kabupatens  = Kabupaten::orderBy('id')->get();
        $groupedData = [];

        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;
            if ($search && stripos($kab->nama_kabupaten, $search) === false) continue;

            $kabData = ['kabupaten' => $kab, 'years' => [], 'records' => [], 'row_total' => 0];

            foreach ($selectedYears as $year) {
                $lbs = LuasBakuSawah::where('kabupaten_id', $kab->id)
                    ->where('tahun', $year)
                    ->first();
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

        return view('user.lbs.index', compact(
            'groupedData', 'totals', 'selectedYears',
            'kabupatenId', 'search', 'kabupatens', 'availableYears', 'sumTotalAll'
        ));
    }

    public function show($id)
    {
        $lbs = LuasBakuSawah::with('kabupaten')->findOrFail($id);

        $otherYears = LuasBakuSawah::where('kabupaten_id', $lbs->kabupaten_id)
            ->where('tahun', '!=', $lbs->tahun)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('user.lbs.show', compact('lbs', 'otherYears'));
    }

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