<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KsaLuasPanen;
use App\Models\Kabupaten;
use App\Exports\KsaPanenTahunanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KsaPanenController extends Controller
{
    public function index(Request $request)
    {
        $bulanList      = KsaLuasPanen::getBulanList();
        $availableYears = KsaLuasPanen::getAvailableYears();

        $bulan      = (int) $request->get('bulan',      (int) date('n'));
        $tahunAwal  = (int) $request->get('tahun_awal',  !empty($availableYears) ? min($availableYears) : date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', !empty($availableYears) ? max($availableYears) : date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();
        $namaBulan   = KsaLuasPanen::getNamaBulan($bulan);

        $rawRows = KsaLuasPanen::where('bulan', $bulan)
            ->whereIn('tahun', $years)
            ->get()
            ->groupBy('kabupaten_id');

        $groupedData = [];
        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;
            $kabData = ['kabupaten' => $kab, 'years' => [], 'total' => 0];
            foreach ($years as $year) {
                $val = (float) ($rawRows->get($kab->id)?->firstWhere('tahun', $year)?->luas_panen ?? 0);
                $kabData['years'][$year] = $val;
                $kabData['total'] += $val;
            }
            $groupedData[] = $kabData;
        }

        $totals = collect();
        foreach ($years as $year) {
            $q = KsaLuasPanen::where('bulan', $bulan)->where('tahun', $year);
            if ($kabupatenId) $q->where('kabupaten_id', $kabupatenId);
            $totals->put($year, (float) $q->sum('luas_panen'));
        }
        $grandTotal = $totals->sum();

        $maxVal = count($groupedData) > 0 ? max(array_column(array_map(function($d) use ($tahunAkhir) {
            return ['v' => $d['years'][$tahunAkhir] ?? 0];
        }, $groupedData), 'v')) : 0;
        $totAkhir       = $totals[$tahunAkhir] ?? 0;
        $rentang        = $tahunAkhir - $tahunAwal + 1;
        $totalKabupaten = count($groupedData);

        return view('user.ksa.panen.tahunan.index', compact(
            'kabupatens', 'bulanList', 'availableYears',
            'bulan', 'namaBulan', 'tahunAwal', 'tahunAkhir',
            'years', 'groupedData', 'totals', 'grandTotal', 'kabupatenId',
            'maxVal', 'totAkhir', 'rentang', 'totalKabupaten'
        ));
    }

    public function export(Request $request)
    {
        $bulan      = (int) $request->get('bulan',      1);
        $tahunAwal  = (int) $request->get('tahun_awal',  date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        $namaBulan = KsaLuasPanen::getNamaBulan($bulan);
        return Excel::download(
            new KsaPanenTahunanExport($bulan, $tahunAwal, $tahunAkhir),
            "ksa-panen-{$namaBulan}-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}