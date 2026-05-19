<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IndeksPertanamanPadi;
use App\Models\Kabupaten;
use App\Exports\IndeksPertanamanPadiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IpPadiController extends Controller
{
    public function index(Request $request)
    {
        $kabupatenId = $request->get('kabupaten_id');
        $search      = $request->get('search', '');

        $availableYears = IndeksPertanamanPadi::getAvailableYears();
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        $tahunList = $availableYears;
        rsort($tahunList);

        $latestYear = max($availableYears);
        $tahunAkhir = $request->get('tahun_akhir', $latestYear);
        $tahunAwal  = $request->get('tahun_awal', max(min($availableYears), $latestYear - 2));

        if ($tahunAwal > $tahunAkhir) {
            [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        }

        $selectedYears = range($tahunAwal, $tahunAkhir);

        foreach ($selectedYears as $year) {
            if (!IndeksPertanamanPadi::where('tahun', $year)->exists()) {
                IndeksPertanamanPadi::syncFromKsaAndLbs($year);
            }
        }

        $kabupatens  = Kabupaten::orderBy('id')->get();
        $groupedData = [];

        foreach ($kabupatens as $kab) {
            if ($kabupatenId && $kab->id != $kabupatenId) continue;
            if ($search && stripos($kab->nama_kabupaten, $search) === false) continue;

            $kabData = ['kabupaten' => $kab, 'years' => [], 'records' => [], 'row_total' => 0];
            foreach ($selectedYears as $year) {
                $ip  = IndeksPertanamanPadi::where('kabupaten_id', $kab->id)->where('tahun', $year)->first();
                $val = $ip ? (float) $ip->ip : 0;
                $kabData['years'][$year]   = $val;
                $kabData['records'][$year] = $ip;
                $kabData['row_total']     += $val;
            }

            $groupedData[] = $kabData;
        }

        $averages       = [];
        $sumAveragesAll = 0;
        foreach ($selectedYears as $year) {
            $records         = IndeksPertanamanPadi::where('tahun', $year)->get();
            $count           = $records->count();
            $total           = $records->sum(fn($r) => (float) $r->ip);
            $avg             = $count > 0 ? $total / $count : 0;
            $averages[$year] = $avg;
            $sumAveragesAll += $avg;
        }

        $latestSelectedYear = empty($selectedYears) ? null : max($selectedYears);
        $ksaDataMap         = [];
        if ($latestSelectedYear) {
            $ksaDataMap = IndeksPertanamanPadi::where('tahun', $latestSelectedYear)
                ->get()->keyBy('kabupaten_id');
        }

        return view('user.ip.padi.index', compact(
            'groupedData', 'averages', 'selectedYears',
            'kabupatenId', 'search', 'kabupatens', 'availableYears',
            'tahunAwal', 'tahunAkhir', 'tahunList', 'ksaDataMap', 'sumAveragesAll'
        ));
    }

    public function show($id)
    {
        $ip = IndeksPertanamanPadi::with('kabupaten')->findOrFail($id);
        return view('user.ip.padi.show', compact('ip'));
    }

    public function export(Request $request)
    {
        $tahunAwal  = $request->get('tahun_awal');
        $tahunAkhir = $request->get('tahun_akhir');

        if ($tahunAwal && $tahunAkhir) {
            $selectedYears = range(min($tahunAwal, $tahunAkhir), max($tahunAwal, $tahunAkhir));
        } else {
            $availableYears = IndeksPertanamanPadi::getAvailableYears();
            $selectedYears  = [max($availableYears) ?? date('Y')];
        }

        $suffix = count($selectedYears) <= 3
            ? implode('-', $selectedYears)
            : min($selectedYears) . '-sampai-' . max($selectedYears);

        return Excel::download(
            new IndeksPertanamanPadiExport($selectedYears),
            "indeks-pertanaman-padi-{$suffix}.xlsx"
        );
    }
}