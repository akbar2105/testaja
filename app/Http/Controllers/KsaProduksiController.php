<?php

namespace App\Http\Controllers;

use App\Models\KsaProduksi;
use App\Models\Kabupaten;
use App\Exports\KsaProduksiTahunanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * SANDING TAHUNAN PER BULAN — READ ONLY
 * Pivot: baris = kabupaten, kolom = tahun
 * Filter: bulan + rentang tahun + kabupaten (semua server-side)
 */
class KsaProduksiController extends Controller
{
    public function index(Request $request)
    {
        $bulanList      = KsaProduksi::getBulanList();
        $availableYears = KsaProduksi::getAvailableYears();

        $bulan      = (int) $request->get('bulan',      1);
        $tahunAwal  = (int) $request->get('tahun_awal',  !empty($availableYears) ? min($availableYears) : date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', !empty($availableYears) ? max($availableYears) : date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();
        $namaBulan   = KsaProduksi::getNamaBulan($bulan);

        // Ambil semua data sekaligus, grouped by kabupaten_id
        $rawRows = KsaProduksi::where('bulan', $bulan)
            ->whereIn('tahun', $years)
            ->get()
            ->groupBy('kabupaten_id');

        // Build groupedData — filter kabupaten di sini (server-side)
        $groupedData = [];
        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;

            $kabData = ['kabupaten' => $kab, 'years' => [], 'total' => 0];
            foreach ($years as $year) {
                $val = (float) ($rawRows->get($kab->id)?->firstWhere('tahun', $year)?->produksi ?? 0);
                $kabData['years'][$year] = $val;
                $kabData['total'] += $val;
            }
            $groupedData[] = $kabData;
        }

        // Total per tahun (footer SUMATERA SELATAN)
        $totals = collect();
        foreach ($years as $year) {
            $yearTotal = 0;
            foreach ($kabupatens as $kab) {
                 if ($kabupatenId && $kab->id != $kabupatenId) continue;
                 $yearTotal += (float) ($rawRows->get($kab->id)?->firstWhere('tahun', $year)?->produksi ?? 0);
            }
            $totals->put($year, $yearTotal);
        }
        $grandTotal = $totals->sum();
        
        $totalKabupaten = count($groupedData);
        $maxVal = collect($groupedData)->max(function($d) use ($tahunAkhir) { return $d['years'][$tahunAkhir] ?? 0; }) ?? 0;
        $totAkhir = $totals->get($tahunAkhir, 0);
        $rentang = $tahunAkhir - $tahunAwal + 1;

        return view('ksa.produksi.tahunan.index', compact(
            'kabupatens', 'bulanList', 'availableYears',
            'bulan', 'namaBulan', 'tahunAwal', 'tahunAkhir',
            'years', 'groupedData', 'totals', 'grandTotal',
            'kabupatenId', 'totalKabupaten', 'maxVal', 'totAkhir', 'rentang'
        ));
    }

    public function export(Request $request)
    {
        $bulan      = (int) $request->get('bulan',      1);
        $tahunAwal  = (int) $request->get('tahun_awal',  date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        $namaBulan = KsaProduksi::getNamaBulan($bulan);

        return Excel::download(
            new KsaProduksiTahunanExport($bulan, $tahunAwal, $tahunAkhir),
            "ksa-produksi-{$namaBulan}-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}