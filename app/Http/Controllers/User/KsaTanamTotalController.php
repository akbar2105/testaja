<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KsaLuasTanam;
use App\Models\Kabupaten;
use App\Exports\KsaTanamTotalExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KsaTanamTotalController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = KsaLuasTanam::getAvailableYears();
        if (empty($availableYears)) $availableYears = range(date('Y') - 6, (int) date('Y'));

        $tahunAwal   = (int) $request->get('tahun_awal',  min($availableYears));
        $tahunAkhir  = (int) $request->get('tahun_akhir', max($availableYears));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();

        $filteredKabupatens = $kabupatenId
            ? $kabupatens->where('id', $kabupatenId)->values()
            : $kabupatens;

        $dbData = KsaLuasTanam::whereIn('tahun', range($tahunAwal, $tahunAkhir + 1));
        if ($kabupatenId) $dbData->where('kabupaten_id', $kabupatenId);
        $rawRows = $dbData->get();

        $data = [];
        $totalPerTahun = collect();
        foreach ($years as $y) {
            $totalPerTahun->put($y, 0);
        }

        foreach ($filteredKabupatens as $kab) {
            $data[$kab->id] = [];
            $kabRows = $rawRows->where('kabupaten_id', $kab->id);
            foreach ($years as $periodTahun) {
                $sum = $kabRows->filter(function($r) use ($periodTahun) {
                    if (in_array($r->bulan, [10,11,12]) && $r->tahun == $periodTahun) return true;
                    if (in_array($r->bulan, range(1,9)) && $r->tahun == $periodTahun + 1) return true;
                    return false;
                })->sum('luas_tanam');
                
                $data[$kab->id][$periodTahun] = (float) $sum;
                $totalPerTahun->put($periodTahun, $totalPerTahun->get($periodTahun) + (float) $sum);
            }
        }

        $grandTotal = $totalPerTahun->sum();
        $totalKabupaten = $filteredKabupatens->count();
        $totAkhir = $totalPerTahun->get($tahunAkhir, 0);
        $maxVal = collect($data)->map(function($d) use ($tahunAkhir) { return $d[$tahunAkhir] ?? 0; })->max() ?? 0;
        $rentang = $tahunAkhir - $tahunAwal + 1;

        return view('user.ksa.tanam.total.index', compact(
            'kabupatens', 'filteredKabupatens', 'availableYears',
            'tahunAwal', 'tahunAkhir', 'years', 'data',
            'totalPerTahun', 'grandTotal', 'kabupatenId',
            'totalKabupaten', 'totAkhir', 'maxVal', 'rentang'
        ));
    }

    public function export(Request $request)
    {
        $tahunAwal  = (int) $request->get('tahun_awal', date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        return Excel::download(
            new KsaTanamTotalExport($tahunAwal, $tahunAkhir),
            "ksa-tanam-total-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}