<?php

namespace App\Http\Controllers;

use App\Models\RekapTahunanTanam;
use App\Models\RekapBulananTanam;
use App\Models\Kabupaten;
use App\Exports\RekapTahunanTanamExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapTahunanTanamController extends Controller
{
    public function index(Request $request)
    {
        // ── Semua tahun yang ada di DB ──────────────────────────────────────
        $tahunList = RekapTahunanTanam::select('tahun')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun');

        // Fallback jika DB kosong: gunakan 5 tahun terakhir
        if ($tahunList->isEmpty()) {
            $tahunList = collect(range(date('Y') - 4, (int) date('Y')));
        }

        // ── Tahun awal & akhir dari request ────────────────────────────────
        $tahunAwal  = (int) $request->get('tahun_awal',  $tahunList->first());
        $tahunAkhir = (int) $request->get('tahun_akhir', $tahunList->last());

        // Pastikan urutan benar
        if ($tahunAwal > $tahunAkhir) {
            [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        }

        // ── Range tahun yang akan ditampilkan ──────────────────────────────
        // Gunakan semua tahun antara awal-akhir, bukan hanya yang ada di DB
        $tahunDipilih = collect(range($tahunAwal, $tahunAkhir));
        $kabupatenId = $request->get('kabupaten_id');

        // ── Data ───────────────────────────────────────────────────────────
        $semuaKabupaten = Kabupaten::orderBy('id')->get();
        $kabupatens = Kabupaten::orderBy('id')
            ->when($kabupatenId, function ($q) use ($kabupatenId) {
                return $q->where('id', $kabupatenId);
            })->get();

        $data = RekapTahunanTanam::whereIn('tahun', $tahunDipilih)
            ->when($kabupatenId, function ($q) use ($kabupatenId) {
                return $q->where('kabupaten_id', $kabupatenId);
            })
            ->get()
            ->groupBy('kabupaten_id');

        $totalPerTahun = RekapTahunanTanam::whereIn('tahun', $tahunDipilih)
            ->when($kabupatenId, function ($q) use ($kabupatenId) {
                return $q->where('kabupaten_id', $kabupatenId);
            })
            ->select('tahun', DB::raw('SUM(total) as grand_total'))
            ->groupBy('tahun')
            ->pluck('grand_total', 'tahun');

        $formattedData = [];
        $maxTotal = 0;

        foreach ($kabupatens as $kab) {
            $kabData = $data->get($kab->id, collect());
            $row = [
                'id'             => $kab->id,
                'nama_kabupaten' => strtoupper($kab->nama_kabupaten),
                'years'          => [],
                'row_total'      => 0
            ];

            foreach ($tahunDipilih as $tahun) {
                $val = (float) ($kabData->firstWhere('tahun', $tahun)->total ?? 0);
                $row['years'][$tahun] = $val;
                $row['row_total'] += $val;

                if ($val > $maxTotal) {
                    $maxTotal = $val;
                }
            }
            $formattedData[] = $row;
        }

        $sumTotal = $totalPerTahun->sum();

        return view('rekap.tahunan.tanam.index', compact(
            'kabupatens',
            'semuaKabupaten',
            'kabupatenId',
            'tahunList',
            'tahunAwal',
            'tahunAkhir',
            'tahunDipilih',
            'formattedData',
            'totalPerTahun',
            'maxTotal',
            'sumTotal'
        ));
    }


    public function export(Request $request)
    {
        $tahunList = RekapTahunanTanam::select('tahun')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun');

        $tahunAwal  = (int) $request->get('tahun_awal',  $tahunList->first() ?? date('Y') - 4);
        $tahunAkhir = (int) $request->get('tahun_akhir', $tahunList->last()  ?? date('Y'));

        if ($tahunAwal > $tahunAkhir) {
            [$tahunAwal, $tahunAkhir] = [$tahunAkhir, $tahunAwal];
        }

        $tahunDipilih = collect(range($tahunAwal, $tahunAkhir))->toArray();
        $filename     = 'rekap_tahunan_tanam_' . $tahunAwal . '-' . $tahunAkhir . '.xlsx';

        return Excel::download(new RekapTahunanTanamExport($tahunDipilih), $filename);
    }
}