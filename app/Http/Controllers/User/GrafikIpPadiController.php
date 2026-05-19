<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\IndeksPertanamanPadi as IpPadi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GrafikIpPadiController extends Controller
{
            private array $chartColors = [
        '#3B82F6','#10B981','#F59E0B','#EF4444',
        '#8B5CF6','#EC4899','#14B8A6','#F97316',
        '#06B6D4','#84CC16','#F43F5E','#A855F7',
        '#0EA5E9','#22D3EE','#FB923C','#4ADE80',
    ];

    private function buildData($kabupatens, array $tahunRange): array
    {
        $data = [];
        $records = IpPadi::whereIn('tahun', $tahunRange)->get()->groupBy('kabupaten_id');

        foreach ($kabupatens as $kab) {
            $row = [
                'kabupaten'       => $kab->nama_kabupaten,
                'kabupaten_short' => $kab->nama_kabupaten,
            ];
            $kabRecords = $records->get($kab->id, collect());
            foreach ($tahunRange as $tahun) {
                $rec = $kabRecords->firstWhere('tahun', $tahun);
                $row[$tahun] = $rec ? round($rec->ip ?? 0, 2) : 0;
            }
            $data[] = $row;
        }
        return $data;
    }

    public function index(Request $request)
    {
        $tahunTersedia = IpPadi::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $kabupatens    = Kabupaten::orderBy('id')->get();

        $tahunAwal  = (int) $request->input('tahun_awal',  $tahunTersedia->min() ?? date('Y') - 4);
        $tahunAkhir = (int) $request->input('tahun_akhir', $tahunTersedia->max() ?? date('Y'));
        $tahunRange = range($tahunAwal, $tahunAkhir);

        $data = $this->buildData($kabupatens, $tahunRange);

        $totalPerTahun = [];
        foreach ($tahunRange as $tahun) {
            $totalPerTahun[$tahun] = round(array_sum(array_column($data, $tahun)), 2);
        }

        $chartColors      = $this->chartColors;
        $chartColorsCount = count($this->chartColors);

        return view('user.grafik.ip.padi', compact('data', 'tahunRange', 'tahunAwal', 'tahunAkhir',
            'totalPerTahun', 'tahunTersedia', 'chartColors', 'chartColorsCount'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tahunAwal  = (int) $request->input('tahun_awal',  IpPadi::min('tahun') ?? date('Y') - 4);
        $tahunAkhir = (int) $request->input('tahun_akhir', IpPadi::max('tahun') ?? date('Y'));
        $tahunRange = range($tahunAwal, $tahunAkhir);
        $kabupatens = Kabupaten::orderBy('id')->get();

        $dataRaw = $this->buildData($kabupatens, $tahunRange);

        $kolom         = array_map('strval', $tahunRange);
        $kolomKey      = $tahunRange;
        $totalPerKolom = [];
        foreach ($tahunRange as $t) {
            $totalPerKolom[$t] = round(array_sum(array_column($dataRaw, $t)), 2);
        }

        $data         = $dataRaw;
        $chartColors  = $this->chartColors;
        $periodeLabel = $tahunAwal . '-' . $tahunAkhir;
        $judulPdf     = 'Indeks Pertanaman (IP) Padi';
        $judulTabel   = 'Tabel IP Padi — ' . $tahunAwal . '-' . $tahunAkhir;
        $headerColor1 = '#10B981';
        $headerColor2 = '#059669';
        $sectionBg    = '#D1FAE5';
        $sectionColor = '#065F46';
        $useDecimals  = true;
        $tanggalCetak = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
                      . ' pukul ' . Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';

        $pdfMode = 'tahunan';
        $metrics = \App\Services\PdfGraphicService::prepareData($data, $kolom, $pdfMode ?? 'bulanan', $judulPdf, $chartColors, $kolomKey ?? null, $useDecimals ?? false, $headerColor1 ?? '#111827', $headerColor2 ?? null, $chartMode ?? null);
        $pdf = Pdf::loadView('user.grafik.pdf.grafik-pdf', compact('metrics', 'data', 'kolom', 'kolomKey', 'totalPerKolom', 'chartColors',
            'judulPdf', 'judulTabel', 'periodeLabel', 'tanggalCetak',
            'headerColor1', 'headerColor2', 'sectionBg', 'sectionColor', 'useDecimals',
            'pdfMode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('ip-padi-' . $periodeLabel . '.pdf');
    }
}