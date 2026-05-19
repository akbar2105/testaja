<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\LuasBakuSawah as Lbs;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GrafikLbsController extends Controller
{
            private array $chartColors = [
        '#3B82F6','#10B981','#F59E0B','#EF4444',
        '#8B5CF6','#EC4899','#14B8A6','#F97316',
        '#06B6D4','#84CC16','#F43F5E','#A855F7',
        '#0EA5E9','#22D3EE','#FB923C','#4ADE80',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    private function buildData($kabupatens, array $tahunRange): array
    {
        $data = [];
        $records = Lbs::whereIn('tahun', $tahunRange)->get()->groupBy('kabupaten_id');

        foreach ($kabupatens as $kab) {
            $row = [
                'kabupaten'       => $kab->nama_kabupaten,
                'kabupaten_short' => $kab->nama_kabupaten,
            ];
            $kabRecords = $records->get($kab->id, collect());
            foreach ($tahunRange as $tahun) {
                $rec      = $kabRecords->firstWhere('tahun', $tahun);
                $row[$tahun] = $rec ? round($rec->luas_baku_sawah ?? 0, 2) : 0;
            }
            $data[] = $row;
        }
        return $data;
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $tahunTersedia = Lbs::distinct()->orderBy('tahun','desc')->pluck('tahun');
        $kabupatens    = Kabupaten::orderBy('id')->get();

        $tahunRange = $request->has('tahun')
            ? array_map('intval', (array) $request->input('tahun'))
            : $tahunTersedia->take(3)->toArray();

        sort($tahunRange);

        $data = $this->buildData($kabupatens, $tahunRange);

        $totalPerTahun = [];
        foreach ($tahunRange as $tahun) {
            $totalPerTahun[$tahun] = round(array_sum(array_column($data, $tahun)), 2);
        }

        $periodeLabel     = count($tahunRange) > 0
            ? min($tahunRange).(count($tahunRange) > 1 ? '-'.max($tahunRange) : '')
            : '-';
        $chartColors      = $this->chartColors;
        $chartColorsCount = count($this->chartColors);

        return view('grafik.sanding.lbs', compact('data','tahunRange','tahunTersedia','totalPerTahun',
            'periodeLabel','chartColors','chartColorsCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $kabupatens = Kabupaten::orderBy('id')->get();
        $tahunRange = $request->has('tahun')
            ? array_map('intval', (array) $request->input('tahun'))
            : Lbs::distinct()->orderBy('tahun')->pluck('tahun')->take(3)->toArray();

        sort($tahunRange);

        $dataRaw = $this->buildData($kabupatens, $tahunRange);

        $kolom         = array_map('strval', $tahunRange);
        $kolomKey      = $tahunRange;
        $totalPerKolom = [];
        foreach ($tahunRange as $t) {
            $totalPerKolom[$t] = round(array_sum(array_column($dataRaw, $t)), 2);
        }

        $data         = $dataRaw;
        $chartColors  = $this->chartColors;
        $periodeLabel = count($tahunRange) > 0
            ? min($tahunRange).(count($tahunRange) > 1 ? '-'.max($tahunRange) : '')
            : '-';
        $judulPdf     = 'Sanding Luas Baku Sawah (LBS)';
        $judulTabel   = 'Tabel Luas Baku Sawah (Ha) — '.$periodeLabel;
        $headerColor1 = '#0D9488';
        $headerColor2 = '#0891B2';
        $sectionBg    = '#CCFBF1';
        $sectionColor = '#134E4A';
        $useDecimals  = true;
        $tanggalCetak = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
                      . ' pukul ' . Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';

        $pdfMode = 'tahunan';
        $metrics = \App\Services\PdfGraphicService::prepareData($data, $kolom, $pdfMode ?? 'bulanan', $judulPdf, $chartColors, $kolomKey ?? null, $useDecimals ?? false, $headerColor1 ?? '#111827', $headerColor2 ?? null, $chartMode ?? null);
        $pdf = Pdf::loadView('grafik.pdf.grafik-pdf', compact('metrics', 'data','kolom','kolomKey','totalPerKolom','chartColors',
            'judulPdf','judulTabel','periodeLabel','tanggalCetak',
            'headerColor1','headerColor2','sectionBg','sectionColor','useDecimals',
            'pdfMode'
        ))->setPaper('a4','landscape');

        return $pdf->download('lbs-'.$periodeLabel.'.pdf');
    }
}