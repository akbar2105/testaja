<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Exports\RekapHarianPanenExport;
use Illuminate\Http\Request;
use App\Models\RekapHarianPanen;

class RekapHarianPanenController extends Controller
{
        public function index(Request $request)
    {
        $dbYears = RekapHarianPanen::selectRaw('YEAR(tanggal) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();

        if (empty($dbYears)) {
            $dbYears = [date('Y')];
        }

        $tahun = (int) $request->get('tahun', $dbYears[0] ?? date('Y'));
        $years = $dbYears;

        $bulan = (int) $request->get('bulan', date('m'));
        $kabupatenId = $request->get('kabupaten_id');
        $kabupatens = \App\Models\Kabupaten::orderBy('id')->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $rekapsData = RekapHarianPanen::with(['kabupaten', 'kecamatan'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($kabupatenId, fn($q) => $q->where('kabupaten_id', $kabupatenId))
            ->get()
            ->keyBy('kecamatan_id');

        $allKecamatan = \App\Models\Kecamatan::with('kabupaten')
            ->when($kabupatenId, fn($q) => $q->where('kabupaten_id', $kabupatenId))
            ->orderBy('kabupaten_id')
            ->orderBy('nama_kecamatan')
            ->get();

        $rekaps = $allKecamatan->map(function ($kec) use ($rekapsData) {
            $row = $rekapsData->get($kec->id) ?? new RekapHarianPanen(['kabupaten_id' => $kec->kabupaten_id, 'kecamatan_id' => $kec->id]);
            $row->kabupaten_id = $kec->kabupaten_id; // paksa mengikuti kabupaten_id asli dari master
            $row->id = $row->id ?? null;
            $row->setRelation('kabupaten', $kec->kabupaten);
            $row->setRelation('kecamatan', $kec);
            return $row;
        });

        /** @var \Illuminate\Support\Collection $groupedRekaps */
        $groupedRekaps = $rekaps->groupBy('kabupaten_id');

        $totals = $this->calculateTotals($rekapsData, $kabupatens);
        
        /** @var array<string, float|int> $grandTotals */
        $grandTotals = $totals['grandTotals'];
        /** @var array<int, array> $kabTotalsData */
        $kabTotalsData = $totals['kabTotalsData'];

        return view('user.rekap.harian.panen.index', compact(
            'groupedRekaps', 'tahun', 'bulan', 'years', 'months',
            'kabupatenId', 'kabupatens', 'grandTotals', 'kabTotalsData'
        ));
    }

    public function export(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        $bulan = (int) $request->get('bulan', date('m'));

        $export = new RekapHarianPanenExport($tahun, $bulan);
        return $export->download();
    }

    private function calculateTotals($rekapsData, $kabupatens)
    {
        /** @var array<string, float|int> $grandTotals */
        $grandTotals = ['target' => 0, 'total_panen' => 0, 'oplah' => 0, 'gogo' => 0, 'csr' => 0, 'total_ltp' => 0, 'realisasi' => 0];
        for ($d = 1; $d <= 31; $d++) $grandTotals["tgl_$d"] = 0;

        /** @var array<int, array> $kabTotalsData */
        $kabTotalsData = [];
        foreach ($kabupatens as $kab) {
            $kabTotalsData[$kab->id] = ['target' => 0, 'total_panen' => 0, 'oplah' => 0, 'gogo' => 0, 'csr' => 0, 'total_ltp' => 0, 'realisasi' => 0];
            for ($d = 1; $d <= 31; $d++) $kabTotalsData[$kab->id]["tgl_$d"] = 0;
        }

        foreach ($rekapsData as $row) {
            $kabId = $row->kabupaten_id;
            if (!isset($kabTotalsData[$kabId])) continue;

            $realisasi = $row->target - $row->total_ltp;

            $kabTotalsData[$kabId]['target'] += (float)$row->target;
            $kabTotalsData[$kabId]['total_panen'] += (float)$row->total_panen;
            $kabTotalsData[$kabId]['oplah'] += (float)$row->oplah;
            $kabTotalsData[$kabId]['gogo'] += (float)$row->gogo;
            $kabTotalsData[$kabId]['csr'] += (float)$row->csr;
            $kabTotalsData[$kabId]['total_ltp'] += (float)$row->total_ltp;
            $kabTotalsData[$kabId]['realisasi'] += (float)$realisasi;

            $grandTotals['target'] += (float)$row->target;
            $grandTotals['total_panen'] += (float)$row->total_panen;
            $grandTotals['oplah'] += (float)$row->oplah;
            $grandTotals['gogo'] += (float)$row->gogo;
            $grandTotals['csr'] += (float)$row->csr;
            $grandTotals['total_ltp'] += (float)$row->total_ltp;
            $grandTotals['realisasi'] += (float)$realisasi;

            for ($d = 1; $d <= 31; $d++) {
                $val = (float)($row->{"tgl_$d"} ?? 0);
                $kabTotalsData[$kabId]["tgl_$d"] += $val;
                $grandTotals["tgl_$d"] += $val;
            }
        }

        return ['grandTotals' => $grandTotals, 'kabTotalsData' => $kabTotalsData];
    }

}