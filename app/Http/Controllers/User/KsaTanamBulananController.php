<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        return view('user.ksa.tanam.bulanan.index', compact(
            'kabupatens', 'filteredKabupatens', 'bulanList',
            'availableYears', 'tahun', 'rows', 'totalPerBulan', 'kabupatenId',
            'totalDataRecords', 'sumTotal', 'maxBulanVal', 'maxBulanNama'
        ));
    }
    
    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        return Excel::download(
            new KsaTanamBulananExport($tahun),
            "ksa-tanam-bulanan-okt{$tahun}-sep" . ($tahun + 1) . ".xlsx"
        );
    }
}