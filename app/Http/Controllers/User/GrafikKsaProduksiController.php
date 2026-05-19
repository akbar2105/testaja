<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\KsaProduksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GrafikKsaProduksiController extends Controller
{
            private array $chartColors = [
        '#3B82F6','#10B981','#F59E0B','#EF4444',
        '#8B5CF6','#EC4899','#14B8A6','#F97316',
        '#06B6D4','#84CC16','#F43F5E','#A855F7',
        '#0EA5E9','#22D3EE','#FB923C','#4ADE80',
    ];

    private array $bulanSingkat = [
        1=>'Jan', 2=>'Feb', 3=>'Mar',  4=>'Apr',
        5=>'Mei', 6=>'Jun', 7=>'Jul',  8=>'Agt',
        9=>'Sep', 10=>'Okt',11=>'Nov',12=>'Des',
    ];

    public function index(Request $request)
    {
        $tab           = $request->input('tab', 'bulanan');
        $tahunTersedia = KsaProduksi::distinct()->orderBy('tahun','desc')->pluck('tahun');
        $kabupatens    = Kabupaten::orderBy('id')->get();

        $dataBulanan  = [];
        $tahunBulanan = null;
        $totalBulanan = array_fill(0, 12, 0.0);

        $dataTahunan  = [];
        $tahunAwalT   = null;
        $tahunAkhirT  = null;
        $totalTahunan = [];
        $tahunRangeT  = [];

        if ($tab === 'bulanan') {
            $tahunBulanan = (int) $request->input('tahun_bulanan', $tahunTersedia->first() ?? date('Y'));
            $recordsBulanan = KsaProduksi::where('tahun', $tahunBulanan)->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten, 'data' => []];
                $slotIdx = 0;
                $kabRecords = $recordsBulanan->get($kab->id, collect());
                foreach ($this->bulanSingkat as $bulanNum => $label) {
                    $rec = $kabRecords->firstWhere('bulan', $bulanNum);
                    $val = $rec ? (float)($rec->produksi ?? 0) : 0.0;
                    $row['data'][]          = $val;
                    $totalBulanan[$slotIdx] += $val;
                    $slotIdx++;
                }
                $dataBulanan[] = $row;
            }
        }

        if ($tab === 'tahunan') {
            $tahunAwalT  = (int) $request->input('tahun_awal_tahunan',  $tahunTersedia->min() ?? date('Y') - 4);
            $tahunAkhirT = (int) $request->input('tahun_akhir_tahunan', $tahunTersedia->max() ?? date('Y'));
            $tahunRangeT = range($tahunAwalT, $tahunAkhirT);
            foreach ($tahunRangeT as $t) $totalTahunan[$t] = 0.0;

            $recordsTahunan = KsaProduksi::whereIn('tahun', $tahunRangeT)
                ->selectRaw('kabupaten_id, tahun, SUM(produksi) as produksi')
                ->groupBy('kabupaten_id', 'tahun')
                ->get()
                ->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $kabRecords = $recordsTahunan->get($kab->id, collect());
                foreach ($tahunRangeT as $t) {
                    $rec = $kabRecords->firstWhere('tahun', $t);
                    $v = $rec ? (float)($rec->produksi ?? 0) : 0.0;
                    $row[$t]           = $v;
                    $totalTahunan[$t] += $v;
                }
                $dataTahunan[] = $row;
            }
        }

        $chartColors      = $this->chartColors;
        $chartColorsCount = count($this->chartColors);
        $bulanLabels      = array_values($this->bulanSingkat);
        $tahunRangeT      = $tahunRangeT ?? [];

        return view('user.grafik.ksa.produksi', compact('tab', 'kabupatens', 'tahunTersedia', 'chartColors', 'chartColorsCount', 'bulanLabels',
            'dataBulanan', 'tahunBulanan', 'totalBulanan',
            'dataTahunan', 'tahunAwalT', 'tahunAkhirT', 'tahunRangeT', 'totalTahunan'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tab       = $request->input('tab', 'bulanan');
        $chartMode = $request->input('chart_mode', 'stacked');
        if (isset($tab) && $tab === 'tahunan') {
            $chartMode = 'grouped';
        }

        $kabupatens = Kabupaten::orderBy('id')->get();

        $chartColors  = $this->chartColors;
        $headerColor1 = '#2563EB';
        $headerColor2 = '#0EA5E9';
        $sectionBg    = '#DBEAFE';
        $sectionColor = '#1E3A8A';
        $useDecimals  = true;
        $tanggalCetak = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
                      . ' pukul ' . Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';

        if ($tab === 'bulanan') {
            $tahunBulanan  = (int) $request->input('tahun_bulanan', KsaProduksi::max('tahun') ?? date('Y'));
            $kolom         = array_values($this->bulanSingkat);
            $totalPerKolom = array_fill_keys($kolom, 0.0);
            $kolomKey      = null;
            $data          = [];

            $recordsBulanan = KsaProduksi::where('tahun', $tahunBulanan)->get()->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $slotIdx = 0;
                $kabRecords = $recordsBulanan->get($kab->id, collect());
                foreach ($this->bulanSingkat as $bulanNum => $label) {
                    $rec = $kabRecords->firstWhere('bulan', $bulanNum);
                    $val = $rec ? (float)($rec->produksi ?? 0) : 0.0;
                    $row[$label]           = $val;
                    $totalPerKolom[$label] += $val;
                    $slotIdx++;
                }
                $data[] = $row;
            }

            $pdfMode      = 'bulanan';
            $periodeLabel = 'Tahun ' . $tahunBulanan;
            $judulPdf     = 'KSA — Produksi per Bulan';
            $judulTabel   = 'Rekapitulasi KSA Produksi Bulanan (Ton-GKG) — Tahun ' . $tahunBulanan;

        } else {
            $tahunAwalT  = (int) $request->input('tahun_awal_tahunan',  KsaProduksi::min('tahun') ?? date('Y') - 4);
            $tahunAkhirT = (int) $request->input('tahun_akhir_tahunan', KsaProduksi::max('tahun') ?? date('Y'));
            $tr          = range($tahunAwalT, $tahunAkhirT);
            $kolom       = array_map('strval', $tr);
            $kolomKey    = $tr;
            $totalPerKolom = array_fill_keys($tr, 0.0);
            $data          = [];

            $recordsTahunan = KsaProduksi::whereIn('tahun', $tr)
                ->selectRaw('kabupaten_id, tahun, SUM(produksi) as produksi')
                ->groupBy('kabupaten_id', 'tahun')
                ->get()
                ->groupBy('kabupaten_id');

            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                $kabRecords = $recordsTahunan->get($kab->id, collect());
                foreach ($tr as $t) {
                    $rec = $kabRecords->firstWhere('tahun', $t);
                    $v = $rec ? (float)($rec->produksi ?? 0) : 0.0;
                    $row[$t]              = $v;
                    $totalPerKolom[$t]   += $v;
                }
                $data[] = $row;
            }

            $pdfMode      = 'tahunan';
            $periodeLabel = $tahunAwalT . '-' . $tahunAkhirT;
            $judulPdf     = 'KSA — Total Produksi per Tahun';
            $judulTabel   = 'Rekapitulasi KSA Total Produksi (Ton-GKG) — ' . $tahunAwalT . '-' . $tahunAkhirT;
        }

        $metrics = \App\Services\PdfGraphicService::prepareData($data, $kolom, $pdfMode ?? 'bulanan', $judulPdf, $chartColors, $kolomKey ?? null, $useDecimals ?? false, $headerColor1 ?? '#111827', $headerColor2 ?? null, $chartMode ?? null);
        $pdf = Pdf::loadView('user.grafik.pdf.grafik-pdf', compact('metrics', 'data', 'kolom', 'kolomKey', 'totalPerKolom', 'chartColors',
            'judulPdf', 'judulTabel', 'periodeLabel', 'tanggalCetak',
            'headerColor1', 'headerColor2', 'sectionBg', 'sectionColor',
            'useDecimals', 'pdfMode', 'chartMode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('ksa-produksi-' . $periodeLabel . '.pdf');
    }
}