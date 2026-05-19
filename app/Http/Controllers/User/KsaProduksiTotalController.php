<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KsaProduksi;
use App\Models\Kabupaten;
use App\Exports\KsaProduksiTotalExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KsaProduksiTotalController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = KsaProduksi::getAvailableYears();
        if (empty($availableYears)) {
            $availableYears = range(date('Y') - 6, (int) date('Y'));
        }

        $tahunAwal   = (int) $request->get('tahun_awal',  min($availableYears));
        $tahunAkhir  = (int) $request->get('tahun_akhir', max($availableYears));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();

        $query = KsaProduksi::whereIn('tahun', $years);
        if ($kabupatenId) $query->where('kabupaten_id', $kabupatenId);
        
        $dbData = $query->groupBy('kabupaten_id', 'tahun')
            ->selectRaw('kabupaten_id, tahun, SUM(produksi) as total_produksi')
            ->get();

        $rawData = collect();
        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;

            $kabData = collect();
            foreach ($years as $year) {
                $total = (float) ($dbData->where('kabupaten_id', $kab->id)->where('tahun', $year)->first()?->total_produksi ?? 0);
                $kabData->push((object)['tahun' => $year, 'total' => $total]);
            }
            $rawData->put($kab->id, $kabData);
        }

        $filteredKabupatens = $kabupatenId
            ? $kabupatens->where('id', $kabupatenId)->values()
            : $kabupatens;

        $totalPerTahun = collect();
        foreach ($years as $year) {
            $yearTotal = (float) $dbData->where('tahun', $year)->sum('total_produksi');
            $totalPerTahun->put($year, $yearTotal);
        }

        $totalPerKabupaten = collect();
        foreach ($filteredKabupatens as $kab) {
            $total = $rawData->get($kab->id)?->sum('total') ?? 0;
            $totalPerKabupaten->put($kab->id, (float) $total);
        }

        $grandTotal = $totalPerTahun->sum();
        $totalKabupaten = $filteredKabupatens->count();
        $totAkhir = $totalPerTahun->get($tahunAkhir, 0);
        $maxVal = $rawData->map(function($r) use ($tahunAkhir) { return $r->firstWhere('tahun', $tahunAkhir)?->total ?? 0; })->max() ?? 0;
        $rentang = $tahunAkhir - $tahunAwal + 1;

        return view('user.ksa.produksi.total.index', compact(
            'kabupatens', 'filteredKabupatens', 'availableYears',
            'tahunAwal', 'tahunAkhir',
            'years', 'rawData', 'totalPerTahun',
            'totalPerKabupaten', 'grandTotal',
            'kabupatenId', 'totalKabupaten', 'totAkhir', 'maxVal', 'rentang'
        ));
    }

    public function export(Request $request)
    {
        $tahunAwal  = (int) $request->get('tahun_awal',  date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        return Excel::download(
            new KsaProduksiTotalExport($tahunAwal, $tahunAkhir),
            "ksa-produksi-total-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}