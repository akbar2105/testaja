<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KsaLuasPanen;
use App\Models\Kabupaten;
use App\Exports\KsaPanenTotalExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KsaPanenTotalController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = KsaLuasPanen::getAvailableYears();
        if (empty($availableYears)) $availableYears = range(date('Y') - 6, (int) date('Y'));

        $tahunAwal   = (int) $request->get('tahun_awal',  min($availableYears));
        $tahunAkhir  = (int) $request->get('tahun_akhir', max($availableYears));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];

        $kabupatenId = $request->get('kabupaten_id') ? (int) $request->get('kabupaten_id') : null;
        $years       = range($tahunAwal, $tahunAkhir);
        $kabupatens  = Kabupaten::orderBy('id')->get();

        $rawData = KsaLuasPanen::whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->select('kabupaten_id', 'tahun', DB::raw('SUM(luas_panen) as total'))
            ->groupBy('kabupaten_id', 'tahun')
            ->get()
            ->groupBy('kabupaten_id');

        $filteredKabupatens = $kabupatenId
            ? $kabupatens->where('id', $kabupatenId)->values()
            : $kabupatens;

        $totalQuery = KsaLuasPanen::whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->select('tahun', DB::raw('SUM(luas_panen) as grand_total'));
        if ($kabupatenId) $totalQuery->where('kabupaten_id', $kabupatenId);
        $totalPerTahun = $totalQuery->groupBy('tahun')->pluck('grand_total', 'tahun');

        $totalPerKabupaten = collect();
        foreach ($filteredKabupatens as $kab) {
            $total = $rawData->get($kab->id)?->sum('total') ?? 0;
            $totalPerKabupaten->put($kab->id, (float) $total);
        }
        $grandTotal = $totalPerTahun->sum();

        $totAkhir       = $totalPerTahun[$tahunAkhir] ?? 0;
        $rentang        = $tahunAkhir - $tahunAwal + 1;
        $totalKabupaten = $filteredKabupatens->count();

        return view('user.ksa.panen.total.index', compact(
            'kabupatens', 'filteredKabupatens', 'availableYears',
            'tahunAwal', 'tahunAkhir', 'years', 'rawData',
            'totalPerTahun', 'totalPerKabupaten', 'grandTotal', 'kabupatenId',
            'totAkhir', 'rentang', 'totalKabupaten'
        ));
    }

    public function export(Request $request)
    {
        $tahunAwal  = (int) $request->get('tahun_awal',  date('Y') - 6);
        $tahunAkhir = (int) $request->get('tahun_akhir', date('Y'));
        if ($tahunAwal > $tahunAkhir) [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        return Excel::download(
            new KsaPanenTotalExport($tahunAwal, $tahunAkhir),
            "ksa-panen-total-{$tahunAwal}-{$tahunAkhir}.xlsx"
        );
    }
}