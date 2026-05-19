<?php

namespace App\Exports;

use App\Models\KsaLuasPanen;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * KSA Sanding Bulanan — Luas Panen
 * Warna : VIOLET / INDIGO  (sesuai image 4-5, BERBEDA dari tanam orange)
 * Kolom : Jan | Feb | Mar | Apr | Mei | Jun | Jul | Agu | Sep | Okt | Nov | Des | Total
 */
class KsaPanenBulananExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    /* ── Palet VIOLET ─────────────────────────────────────────────────── */
    const V600   = '7C3AED'; // violet-600 → judul & header tabel
    const V700   = '6D28D9'; // violet-700 → subjudul
    const V500   = '8B5CF6'; // violet-500 → baris nama bulan
    const V800   = '5B21B6'; // violet-800 → footer
    const V900   = '4C1D95'; // violet-900 → grand-total cell
    const V50    = 'F5F3FF'; // violet-50  → old zebra even
    const ROW_BG = 'F9FAFB';
    const V100   = 'EDE9FE'; // violet-100 → Total col
    const WHITE  = 'FFFFFF';
    const FMT    = '#,##0.##';

    const BULAN_URUT  = [1,2,3,4,5,6,7,8,9,10,11,12];
    const BULAN_LABEL = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];

    public function __construct(protected int $tahun) {}
    public function title(): string
    {
        return "Jan-Des {$this->tahun}";
    }

    private function formatData($val) {
        if ($val === null || $val === '' || $val == 0) {
            return 0;
        } elseif (floor($val) == $val) {
            return (int) $val;
        } else {
            return (float) $val;
        }
    }

    public function array(): array
    {
        $kabupatens = Kabupaten::orderBy('id')->get();
        $data = KsaLuasPanen::where('tahun',$this->tahun)->get()->groupBy('kabupaten_id');
        $totalBulan = KsaLuasPanen::where('tahun',$this->tahun)
            ->select('bulan',DB::raw('SUM(luas_panen) as t'))->groupBy('bulan')->pluck('t','bulan');

        $out = [];
        $out[] = ['KSA LUAS PANEN PADI (HEKTAR) — SANDING BULANAN JANUARI–DESEMBER'];
        $out[] = ['Tahun: '.$this->tahun.'   |   Satuan: Hektar (Ha)'];
        $out[] = array_fill(0, 15, '');
        $out[] = array_merge(['No','Kabupaten/Kota','Luas Panen Padi (Ha) Tahun '.$this->tahun], array_fill(0,11,''), ['Total']);
        $bulanRow = ['',''];
        foreach (self::BULAN_URUT as $bn) $bulanRow[] = self::BULAN_LABEL[$bn];
        $bulanRow[] = '';
        $out[] = $bulanRow;

        $no = 1;
        foreach ($kabupatens as $kab) {
            $recs = $data->get($kab->id,collect());
            $row  = [$no++, strtoupper($kab->nama_kabupaten)];
            $sum  = 0;
            foreach (self::BULAN_URUT as $bn) {
                $v     = (float) ($recs->firstWhere('bulan', $bn)?->luas_panen ?? 0);
                $sum  += $v;
                $row[] = $this->formatData($v);
            }
            $row[]  = $this->formatData($sum);
            $out[] = $row;
        }

        $footer = ['SUMATERA SELATEN', null];
        $grand  = 0;
        foreach (self::BULAN_URUT as $bn) {
            $v        = (float) $totalBulan->get($bn, 0);
            $grand   += $v;
            $footer[] = $this->formatData($v);
        }
        $footer[] = $this->formatData($grand);
        $out[]    = $footer;
        $out[] = [];
        $out[] = ['* Satuan: Hektar (Ha)   |   Tahun: '.$this->tahun];
        return $out;
    }

    public function styles(Worksheet $sheet): array
    {
        $nKab = Kabupaten::count();
        $lastCol = 'O'; $totCol = 'O';
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $dS = 6; $dE = $dS + $nKab - 1; $fR = $dE + 1; $nR = $fR + 2;

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:N4');
        $sheet->mergeCells('O4:O5');

        $sheet->getStyle('A1')->applyFromArray(['font'=>['bold'=>true,'size'=>14,'color'=>['rgb'=>'111827'],'name'=>'Arial'],'fill'=>['fillType'=>Fill::FILL_NONE],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getStyle('A2')->applyFromArray(['font'=>['bold'=>true,'size'=>11,'color'=>['rgb'=>'374151'],'name'=>'Arial'],'fill'=>['fillType'=>Fill::FILL_NONE],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::V600]],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::V700]]]]);
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getStyle("C5:N5")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::V500]],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::V700]]]]);
        $sheet->getRowDimension(5)->setRowHeight(18);

        for ($r=$dS;$r<=$dE;$r++) {
            $bg = (($r-$dS)%2===0) ? self::WHITE : self::ROW_BG;
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'E5E7EB']]],'font'=>['name'=>'Arial','size'=>9]]);
            $sheet->getStyle("A{$r}")->applyFromArray(['alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER],'font'=>['bold'=>true,'color'=>['rgb'=>'374151']]]);
            $sheet->getStyle("B{$r}")->applyFromArray(['alignment'=>['horizontal'=>Alignment::HORIZONTAL_LEFT],'font'=>['bold'=>true,'color'=>['rgb'=>'111827']]]);
            $sheet->getStyle("C{$r}:N{$r}")->applyFromArray(['alignment'=>['horizontal'=>Alignment::HORIZONTAL_RIGHT]]);
            $sheet->getStyle("{$totCol}{$r}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::V100]],'font'=>['bold'=>true,'color'=>['rgb'=>self::V900],'name'=>'Arial','size'=>9],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_RIGHT],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'BFDBFE']]]]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        $sheet->mergeCells("A{$fR}:B{$fR}");
        $sheet->getStyle("A{$fR}:{$lastCol}{$fR}")->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::V800]],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_MEDIUM,'color'=>['rgb'=>self::V900]]]]);
        $sheet->getStyle("C{$fR}:N{$fR}")->applyFromArray(['alignment'=>['horizontal'=>Alignment::HORIZONTAL_RIGHT]]);
        $sheet->getStyle("{$totCol}{$fR}")->applyFromArray(['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::V900]],'font'=>['bold'=>true,'color'=>['rgb'=>self::WHITE]],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_RIGHT]]);
        $sheet->getRowDimension($fR)->setRowHeight(22);
        $sheet->getStyle("A{$nR}")->applyFromArray(['font'=>['italic'=>true,'size'=>8,'color'=>['rgb'=>'9CA3AF']]]);
        $sheet->freezePane('A6');

        $lastLet = isset($lastColLtr) ? $lastColLtr : (isset($lastCol) ? $lastCol : 'O');
        $range = "C" . $dS . ":" . $lastLet . $fR;
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"0"');
        $dataArray = $sheet->rangeToArray($range, null, false, false);
        $rangeBoundaries = Coordinate::rangeBoundaries($range);
        $cStart = $rangeBoundaries[0][0];
        $rStart = $rangeBoundaries[0][1];
        foreach ($dataArray as $rIdx => $rowArray) {
            $r = $rStart + $rIdx;
            foreach ($rowArray as $cIdx => $val) {
                $c = $cStart + $cIdx;
                if ($val === null || $val === '') {
                    $sheet->setCellValueExplicit([$c, $r], 0, DataType::TYPE_NUMERIC);
                }
            }
        }
        $topLeft = explode(':', $range)[0];
        $cond = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $cond->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
        $cond->setConditions(['=' . $topLeft . '<>INT(' . $topLeft . ')']);
        $cond->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
        $conditionalStyles = $sheet->getStyle($range)->getConditionalStyles();
        $conditionalStyles[] = $cond;
        $sheet->getStyle($range)->setConditionalStyles($conditionalStyles);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A'=>4,'B'=>26,'C'=>8,'D'=>8,'E'=>8,'F'=>8,'G'=>8,'H'=>8,'I'=>8,'J'=>8,'K'=>8,'L'=>8,'M'=>8,'N'=>8,'O'=>13];
    }
}