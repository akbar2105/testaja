<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\RekapBulananTanam;
use App\Models\RekapHarianTanam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GrafikLttTanamController extends Controller
{
    private array $chartColors = [
        '#F59E0B','#EF4444','#3B82F6','#10B981','#8B5CF6','#EC4899',
        '#14B8A6','#F97316','#06B6D4','#84CC16','#F43F5E','#A855F7',
        '#0EA5E9','#22D3EE','#FB923C','#4ADE80',
    ];

    private array $bulanList = [
        'januari','februari','maret','april','mei','juni',
        'juli','agustus','september','oktober','november','desember',
    ];

    private array $bulanLabel = [
        1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
        7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des',
    ];

    private array $bulanNama = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'harian');
        $tahunTersedia = RekapBulananTanam::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $kabupatens = Kabupaten::orderBy('nama_kabupaten')->get();
        $totalKab = $kabupatens->count();

        $dataBulanan = []; $tahunBulanan = null; $totalBulanan = array_fill(0, 12, 0);
        if ($tab === 'bulanan' || $tab === 'all') {
            $tahunBulanan = (int) $request->input('tahun_bulanan', $tahunTersedia->first() ?? date('Y'));
            $recordsBulanan = RekapBulananTanam::where('tahun', $tahunBulanan)->get()->groupBy('kabupaten_id');
            foreach ($kabupatens as $kab) {
                $rekap = $recordsBulanan->get($kab->id)?->first();
                $row = ['kabupaten' => $kab->nama_kabupaten, 'data' => []];
                foreach ($this->bulanList as $i => $bulan) {
                    $val = $rekap ? ($rekap->$bulan ?? 0) : 0;
                    $row['data'][] = (float) $val; $totalBulanan[$i] += $val;
                }
                $dataBulanan[] = $row;
            }
        }

        $dataTahunan = []; $tahunAwalT = null; $tahunAkhirT = null;
        $totalTahunan = []; $tahunRangeT = [];
        if ($tab === 'tahunan' || $tab === 'all') {
            $tahunAwalT  = (int) $request->input('tahun_awal_tahunan',  $tahunTersedia->min() ?? date('Y') - 5);
            $tahunAkhirT = (int) $request->input('tahun_akhir_tahunan', $tahunTersedia->max() ?? date('Y'));
            $tahunRangeT = range($tahunAwalT, $tahunAkhirT);
            foreach ($tahunRangeT as $t) $totalTahunan[$t] = 0;
            
            $recordsTahunan = RekapBulananTanam::whereIn('tahun', $tahunRangeT)->get()->groupBy('kabupaten_id');
            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                foreach ($tahunRangeT as $t) {
                    $rekap = $recordsTahunan->get($kab->id)?->firstWhere('tahun', $t);
                    $total = 0;
                    if ($rekap) foreach ($this->bulanList as $b) $total += $rekap->$b ?? 0;
                    $row[$t] = $total; $totalTahunan[$t] += $total;
                }
                $dataTahunan[] = $row;
            }
        }

        $dataHarian = []; $tahunHarian = null; $bulanHarian = null;
        $totalHarian = []; $jumlahHariBulan = 31;
        if ($tab === 'harian' || $tab === 'all') {
            $tahunHarian     = (int) $request->input('tahun_harian', date('Y'));
            $bulanHarian     = (int) $request->input('bulan_harian', date('n'));
            $jumlahHariBulan = 31;
            
            $recordsHarian = RekapHarianTanam::whereYear('tanggal', $tahunHarian)
                ->whereMonth('tanggal', $bulanHarian)->get()->groupBy('kabupaten_id');
                
            foreach ($kabupatens as $kab) {
                $rekapRows = $recordsHarian->get($kab->id, collect());
                $row = ['kabupaten' => $kab->nama_kabupaten, 'total' => 0];
                for ($d = 1; $d <= $jumlahHariBulan; $d++) {
                    $field = 'tgl_' . $d; $dayTotal = 0;
                    foreach ($rekapRows as $r) $dayTotal += $r->$field ?? 0;
                    $row[$field] = (float) $dayTotal; $row['total'] += $dayTotal;
                }
                $dataHarian[] = $row; $totalHarian[] = (float) $row['total'];
            }
        }

        $chartColors = $this->chartColors;
        $chartColorsCount = count($this->chartColors);
        $bulanLabels = array_values($this->bulanLabel);
        $tahunRangeT = $tahunRangeT ?? [];

        return view('grafik.ltt.tanam', compact(
            'tab','kabupatens','tahunTersedia','chartColors','chartColorsCount',
            'bulanLabels','totalKab','dataBulanan','tahunBulanan','totalBulanan',
            'dataTahunan','tahunAwalT','tahunAkhirT','tahunRangeT','totalTahunan',
            'dataHarian','tahunHarian','bulanHarian','totalHarian','jumlahHariBulan'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tab = $request->input('tab', 'harian');

        /* chart_mode: ikuti pilihan user di view */
        $chartMode = $request->input('chart_mode', 'stacked');
        if (isset($tab) && $tab === 'tahunan') {
            $chartMode = 'grouped';
        }

        $kabupatens   = Kabupaten::orderBy('nama_kabupaten')->get();
        $chartColors  = $this->chartColors;
        $headerColor1 = '#F97316';
        $headerColor2 = '#F59E0B';
        $sectionBg    = '#FFF7ED';
        $sectionColor = '#7C2D12';
        $useDecimals  = false;
        $tanggalCetak = Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
            . ' pukul ' . Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';

        if ($tab === 'bulanan') {
            $tahunBulanan  = (int) $request->input('tahun_bulanan', RekapBulananTanam::max('tahun') ?? date('Y'));
            $kolom         = array_values($this->bulanLabel);
            $kolomKey      = [];
            $totalPerKolom = array_fill_keys($kolom, 0);
            $data          = [];
            
            $recordsBulanan = RekapBulananTanam::where('tahun', $tahunBulanan)->get()->groupBy('kabupaten_id');
            foreach ($kabupatens as $kab) {
                $rekap = $recordsBulanan->get($kab->id)?->first();
                $row   = ['kabupaten' => $kab->nama_kabupaten];
                foreach ($this->bulanList as $i => $b) {
                    $val = $rekap ? ($rekap->$b ?? 0) : 0;
                    $row[$kolom[$i]] = (float) $val;
                    $totalPerKolom[$kolom[$i]] += $val;
                }
                $data[] = $row;
            }
            $periodeLabel = 'Tahun ' . $tahunBulanan;
            $pdfMode      = 'bulanan';
            $judulPdf     = 'LTT — Luas Tanam per Bulan';
            $judulTabel   = 'Rekapitulasi Luas Tanam Bulanan (Ha) — Tahun ' . $tahunBulanan;

        } elseif ($tab === 'tahunan') {
            $tahunAwalT    = (int) $request->input('tahun_awal_tahunan',  RekapBulananTanam::min('tahun') ?? date('Y') - 5);
            $tahunAkhirT   = (int) $request->input('tahun_akhir_tahunan', RekapBulananTanam::max('tahun') ?? date('Y'));
            $tr            = range($tahunAwalT, $tahunAkhirT);
            $kolom         = array_map('strval', $tr);
            $kolomKey      = $tr;
            $totalPerKolom = array_fill_keys($tr, 0);
            $data          = [];
            
            $recordsTahunan = RekapBulananTanam::whereIn('tahun', $tr)->get()->groupBy('kabupaten_id');
            foreach ($kabupatens as $kab) {
                $row = ['kabupaten' => $kab->nama_kabupaten];
                foreach ($tr as $t) {
                    $rekap = $recordsTahunan->get($kab->id)?->firstWhere('tahun', $t);
                    $v = 0;
                    if ($rekap) foreach ($this->bulanList as $b) $v += $rekap->$b ?? 0;
                    $row[$t] = (float) $v; $totalPerKolom[$t] += (float) $v;
                }
                $data[] = $row;
            }
            $periodeLabel = $tahunAwalT . '-' . $tahunAkhirT;
            $pdfMode      = 'tahunan';
            $judulPdf     = 'LTT — Total Luas Tanam per Tahun';
            $judulTabel   = 'Rekapitulasi Total Luas Tanam Tahunan (Ha) — ' . $tahunAwalT . '-' . $tahunAkhirT;

        } else {
            $tahunHarian   = (int) $request->input('tahun_harian', date('Y'));
            $bulanHarian   = (int) $request->input('bulan_harian', date('n'));
            $jumlahHari = 31;
            $kolom         = array_map('strval', range(1, $jumlahHari));
            $kolomKey      = [];
            $totalPerKolom = array_fill_keys($kolom, 0);
            $data          = [];
            
            $recordsHarian = RekapHarianTanam::whereYear('tanggal', $tahunHarian)
                ->whereMonth('tanggal', $bulanHarian)->get()->groupBy('kabupaten_id');
                
            foreach ($kabupatens as $kab) {
                $rows = $recordsHarian->get($kab->id, collect());
                $row = ['kabupaten' => $kab->nama_kabupaten];
                foreach ($kolom as $d) {
                    $v = 0;
                    foreach ($rows as $r) $v += $r->{'tgl_' . $d} ?? 0;
                    $row[$d] = (float) $v; $totalPerKolom[$d] += (float) $v;
                }
                $data[] = $row;
            }
            $bl           = $this->bulanNama[$bulanHarian] ?? '';
            $periodeLabel = $bl . ' ' . $tahunHarian;
            $pdfMode      = 'harian';
            $judulPdf     = 'LTT — Luas Tanam Harian';
            $judulTabel   = 'Rekapitulasi Luas Tanam Harian (Ha) — ' . $periodeLabel;
        }

        $pdf = Pdf::loadView('grafik.pdf.grafik-pdf', compact(
            'data','kolom','kolomKey','totalPerKolom','chartColors',
            'judulPdf','judulTabel','periodeLabel','tanggalCetak',
            'headerColor1','headerColor2','sectionBg','sectionColor',
            'useDecimals','pdfMode','chartMode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('ltt-tanam-' . str_replace(' ', '-', strtolower($periodeLabel)) . '.pdf');
    }
}