<?php

namespace App\Http\Controllers;

use App\Models\KsaLuasTanam;
use App\Models\Kabupaten;
use App\Exports\KsaTanamTahunanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KsaTanamController extends Controller
{
    public function index(Request $request)
    {
        $bulanList      = KsaLuasTanam::getBulanList();
        $availableYears = KsaLuasTanam::getAvailableYears();

        $bulan      = (int) $request->get('bulan',      10);
        $tahunAwal  = (int) $request->get('tahun_awal',  !empty($availableYears) ? min($availableYears) : date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', !empty($availableYears) ? max($availableYears) : date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();
        $namaBulan   = KsaLuasTanam::getNamaBulan($bulan);

        // Untuk tanam: tahun di DB berbeda tergantung bulan
        // Bulan 10,11,12 → stored at tahun T; bulan 1-9 → stored at tahun T+1
        // Kita query by bulan saja, ambil tahun sesuai
        $rawRows = collect();
        foreach ($years as $periodTahun) {
            $dbTahun = in_array($bulan, [10,11,12]) ? $periodTahun : $periodTahun + 1;
            $recs = KsaLuasTanam::where('bulan', $bulan)->where('tahun', $dbTahun)->get();
            foreach ($recs as $rec) {
                if (!$rawRows->has($rec->kabupaten_id)) $rawRows->put($rec->kabupaten_id, collect());
                $rawRows->get($rec->kabupaten_id)->push(['tahun' => $periodTahun, 'val' => (float) $rec->luas_tanam]);
            }
        }

        $groupedData = [];
        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;
            $kabData = ['kabupaten' => $kab, 'years' => [], 'total' => 0];
            $kabRecs = $rawRows->get($kab->id, collect());
            foreach ($years as $year) {
                $val = (float) ($kabRecs->firstWhere('tahun', $year)['val'] ?? 0);
                $kabData['years'][$year] = $val;
                $kabData['total'] += $val;
            }
            $groupedData[] = $kabData;
        }

        $totals = collect();
        foreach ($years as $year) {
            $dbTahun = in_array($bulan, [10,11,12]) ? $year : $year + 1;
            $q = KsaLuasTanam::where('bulan', $bulan)->where('tahun', $dbTahun);
            if ($kabupatenId) $q->where('kabupaten_id', $kabupatenId);
            $totals->put($year, (float) $q->sum('luas_tanam'));
        }
        $grandTotal = $totals->sum();

        $totalKabupaten = count($groupedData);
        $maxVal = collect($groupedData)->max(function($d) use ($tahunAkhir) { return $d['years'][$tahunAkhir] ?? 0; }) ?? 0;
        $totAkhir = $totals->get($tahunAkhir, 0);
        $rentang = $tahunAkhir - $tahunAwal + 1;

        return view('ksa.tanam.tahunan.index', compact(
            'kabupatens', 'bulanList', 'availableYears',
            'bulan', 'namaBulan', 'tahunAwal', 'tahunAkhir',
            'years', 'groupedData', 'totals', 'grandTotal', 'kabupatenId',
            'totalKabupaten', 'maxVal', 'totAkhir', 'rentang'
        ));
    }

    public function export(Request $request)
    {
        $bulan      = (int) $request->get('bulan', 10);
        $tahunAwal  = (int) $request->get('tahun_awal', date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        $namaBulan = KsaLuasTanam::getNamaBulan($bulan);
        return Excel::download(
            new KsaTanamTahunanExport($bulan, $tahunAwal, $tahunAkhir),
            "ksa-tanam-{$namaBulan}-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}