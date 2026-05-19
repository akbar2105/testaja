<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekapBulananTanam;
use App\Models\RekapHarianTanam;
use App\Models\Kabupaten;
use App\Exports\RekapBulananTanamExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapBulananTanamController extends Controller
{
    /**
     * Ambil daftar tahun dari data yang sudah ada di DB.
     * Jika DB kosong, fallback ke tahun saat ini.
     */
    private function getAvailableYears(): array
    {
        $years = RekapBulananTanam::select('tahun')
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
        $search = $request->get('search');

        // Query rekap bulanan
        $rekapsData = RekapBulananTanam::with('kabupaten')
            ->where('tahun', $tahun)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('kabupaten', function ($a) use ($search) {
                    $a->where('nama_kabupaten', 'like', "%$search%");
                });
            })
            ->get()
            ->keyBy('kabupaten_id');

        $allKabupatens = Kabupaten::orderBy('id')->get();
        if ($search) {
            $allKabupatens = $allKabupatens->filter(fn($k) => stripos($k->nama_kabupaten, $search) !== false);
        }

        $rekaps = $allKabupatens->map(function($kab) use ($rekapsData, $tahun) {
            $row = $rekapsData->get($kab->id) ?? new RekapBulananTanam(['kabupaten_id' => $kab->id, 'tahun' => $tahun]);
            $row->id = $row->id ?? null;
            $row->setRelation('kabupaten', $kab);
            return $row;
        });

        // Hitung total keseluruhan per bulan
        $totals = RekapBulananTanam::where('tahun', $tahun)
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

        $allKabupatens = Kabupaten::orderBy('id')->get();

        return view('user.rekap.bulanan.tanam.index', compact(
            'rekaps',
            'tahun',
            'years',
            'search',
            'totals',
            'allKabupatens'
        ));
    }

    /**
     * Show detail per kabupaten
     */
    public function show(Request $request, $id)
    {
        $rekapBulananTanam = RekapBulananTanam::with('kabupaten')->findOrFail($id);

        $bulan = $request->get('bulan', date('m'));

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $harianData = RekapHarianTanam::with('kecamatan')
            ->where('kabupaten_id', $rekapBulananTanam->kabupaten_id)
            ->whereYear('tanggal', $rekapBulananTanam->tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get();

        return view('user.rekap.bulanan.tanam.show', compact(
            'rekapBulananTanam',
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

        return (new RekapBulananTanamExport($tahun))->download();
    }
}