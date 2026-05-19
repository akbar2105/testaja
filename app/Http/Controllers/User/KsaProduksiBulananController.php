<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KsaProduksi;
use App\Models\Kabupaten;
use App\Exports\KsaProduksiBulananExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KsaProduksiBulananController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = KsaProduksi::getAvailableYears();
        $tahun       = (int) $request->get('tahun', $availableYears[0] ?? date('Y'));
        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;

        $kabupatens = Kabupaten::orderBy('id')->get();
        $bulanList  = KsaProduksi::getBulanList();

        $query = KsaProduksi::where('tahun', $tahun);
        if ($kabupatenId) $query->where('kabupaten_id', $kabupatenId);
        $rows = $query->get()->groupBy('kabupaten_id');

        $totalQuery = KsaProduksi::where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(produksi) as total'));
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

        return view('user.ksa.produksi.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(new KsaProduksiBulananExport($tahun), "ksa-produksi-bulanan-{$tahun}.xlsx");
    }
}