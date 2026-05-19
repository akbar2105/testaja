<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapBulananPanen;
use App\Models\RekapHarianPanen;
use App\Models\Kabupaten;
use App\Exports\RekapBulananPanenExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapBulananPanenController extends Controller
{
    /**
     * Ambil daftar tahun dari data yang sudah ada di DB.
     * Jika DB kosong, fallback ke tahun saat ini.
     */
    private function getAvailableYears(): array
    {
        $years = RekapBulananPanen::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        return $years;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $years  = $this->getAvailableYears();

        // Default ke tahun terbaru yang ada di DB
        $tahun  = $request->get('tahun', $years[0] ?? date('Y'));
        $kabupatenId = $request->get('kabupaten_id');
        $kabupatens = Kabupaten::orderBy('id')->get();

        // Query rekap bulanan
        $rekapsData = RekapBulananPanen::with('kabupaten')
            ->where('tahun', $tahun)
            ->when($kabupatenId, function ($q) use ($kabupatenId) {
                $q->where('kabupaten_id', $kabupatenId);
            })
            ->get()
            ->keyBy('kabupaten_id');

        $allKabupatens = Kabupaten::orderBy('id')->get();
        if ($kabupatenId) {
            $allKabupatens = $allKabupatens->filter(fn($k) => $k->id == $kabupatenId);
        }

        $rekaps = $allKabupatens->map(function($kab) use ($rekapsData, $tahun) {
            $row = $rekapsData->get($kab->id) ?? new RekapBulananPanen(['kabupaten_id' => $kab->id, 'tahun' => $tahun]);
            $row->id = $row->id ?? null;
            $row->setRelation('kabupaten', $kab);
            return $row;
        });

        // Hitung total keseluruhan per bulan
        $totals = RekapBulananPanen::where('tahun', $tahun)
            ->when($kabupatenId, function ($q) use ($kabupatenId) {
                $q->where('kabupaten_id', $kabupatenId);
            })
            ->selectRaw('
                SUM(januari) as total_januari,
                SUM(februari) as total_februari,
                SUM(maret) as total_maret,
                SUM(april) as total_april,
                SUM(mei) as total_mei,
                SUM(juni) as total_juni,
                SUM(juli) as total_juli,
                SUM(agustus) as total_agustus,
                SUM(september) as total_september,
                SUM(oktober) as total_oktober,
                SUM(november) as total_november,
                SUM(desember) as total_desember,
                SUM(total) as grand_total
            ')
            ->first();

        return view('rekap.bulanan.panen.index', compact(
            'rekaps',
            'tahun',
            'years',
            'kabupatenId',
            'kabupatens',
            'totals'
        ));
    }

    /**
     * Show detail per kabupaten
     */
    public function show(Request $request, $id)
    {
        $rekapBulananPanen = RekapBulananPanen::with('kabupaten')->findOrFail($id);

        $bulan = $request->get('bulan', date('m'));

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $harianData = RekapHarianPanen::with('kecamatan')
            ->where('kabupaten_id', $rekapBulananPanen->kabupaten_id)
            ->whereYear('tanggal', $rekapBulananPanen->tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get();

        return view('rekap.bulanan.panen.show', compact(
            'rekapBulananPanen',
            'harianData',
            'bulan',
            'namaBulan'
        ));
    }

    /**
     * Export ke Excel
     */
    public function export(Request $request)
    {
        $years = $this->getAvailableYears();
        $tahun = $request->get('tahun', $years[0] ?? date('Y'));

        return (new RekapBulananPanenExport($tahun))->download();
    }
}