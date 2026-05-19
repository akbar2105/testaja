<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        return view('user.ksa.panen.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(new KsaPanenBulananExport($tahun), "ksa-panen-bulanan-{$tahun}.xlsx");
    }
}