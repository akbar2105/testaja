<?php

namespace App\Exports;

use App\Models\KsaLuasTanam;
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
 * KSA Sanding Bulanan — Luas Tanam  (Okt T – Sep T+1)
 * FIX: judul plain (no bg), no subjudul, Total selalu tampil (0 jadi 0 bukan null),
 *      border tegas di semua sel
 */
class KsaTanamBulananExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    const ORANGE_600 = 'EA580C';
    const ORANGE_700 = 'C2410C';
    const ORANGE_500 = 'F97316';
    const ORANGE_400 = 'FB923C';
    const ORANGE_800 = '9A3412';
    const ORANGE_900 = '7C2D12';
    const ORANGE_50  = 'FFF7ED';
    const ORANGE_100 = 'FFEDD5';
    const WHITE      = 'FFFFFF';
    const GRAY_100   = 'F9FAFB';
    const FMT        = '#,##0.##';

    const BULAN_URUT  = [10,11,12,1,2,3,4,5,6,7,8,9];
    const BULAN_LABEL = [
        10=>'Okt',11=>'Nov',12=>'Des',
         1=>'Jan', 2=>'Feb', 3=>'Mar',
         4=>'Apr', 5=>'Mei', 6=>'Jun',
         7=>'Jul', 8=>'Agu', 9=>'Sep',
    ];

    public function __construct(protected int $tahun) {}

    public function title(): string
    {
        return "Okt{$this->tahun}-Sep".($this->tahun+1);
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

        $data = KsaLuasTanam::where(function ($q) {
            $q->where(fn($q2)=>$q2->where('tahun',$this->tahun)->whereIn('bulan',[10,11,12]))
              ->orWhere(fn($q2)=>$q2->where('tahun',$this->tahun+1)->whereBetween('bulan',[1,9]));
        })->get()->groupBy('kabupaten_id');

        $totalBulan = KsaLuasTanam::where(function ($q) {
            $q->where(fn($q2)=>$q2->where('tahun',$this->tahun)->whereIn('bulan',[10,11,12]))
              ->orWhere(fn($q2)=>$q2->where('tahun',$this->tahun+1)->whereBetween('bulan',[1,9]));
        })->select('bulan',DB::raw('SUM(luas_tanam) as t'))->groupBy('bulan')->pluck('t','bulan');

        $out = [];

        // Row 1 – JUDUL PLAIN (no background, teks gelap)
        $out[] = ['KSA LTT PADI (HEKTAR) — SANDING BULANAN OKTOBER '.$this->tahun.' – SEPTEMBER '.($this->tahun+1)];

        // Row 2 – subtitle
        $out[] = ['Periode: Oktober '.$this->tahun.' – September '.($this->tahun+1).'   |   Satuan: Hektar (Ha)'];

        // Row 3 – spacer (pastikan baris kosong tetap dibuat)
        $out[] = array_fill(0, 15, '');

        // Row 4 – header tabel tunggal dengan nama bulan
        $out[] = array_merge(['No','Kabupaten/Kota','LTT Padi (Ha) Tahun '.$this->tahun], array_fill(0,11,''), ['Total']);
        $bulanRow = ['',''];
        foreach (self::BULAN_URUT as $bn) $bulanRow[] = self::BULAN_LABEL[$bn];
        $bulanRow[] = '';
        $out[] = $bulanRow;

        // Data — TOTAL SELALU TAMPIL (0 tetap 0, bukan null)
        $no = 1;
        foreach ($kabupatens as $kab) {
            $recs   = $data->get($kab->id, collect());
            $row    = [$no++, strtoupper($kab->nama_kabupaten)];
            $rowSum = 0;
            foreach (self::BULAN_URUT as $bn) {
                $v       = (float)($recs->firstWhere('bulan',$bn)?->luas_tanam ?? 0);
                $rowSum += $v;
                $row[]   = $this->formatData($v);
            }
            $row[]  = $this->formatData($rowSum);
            $out[]  = $row;
        }

        // Footer Sumatera Selatan
        $footer = ['SUMATERA SELATAN', ''];
        $grand  = 0;
        foreach (self::BULAN_URUT as $bn) {
            $v       = (float)$totalBulan->get($bn,0);
            $grand  += $v;
            $footer[] = $this->formatData($v);
        }
        $footer[] = $this->formatData($grand);
        $out[]    = $footer;
        $out[]    = [];
        $out[]    = ['* Satuan: Hektar (Ha)   |   Periode: Oktober '.$this->tahun.' – September '.($this->tahun+1)];
        return $out;
    }

    public function styles(Worksheet $sheet): array
    {
        $nKab     = Kabupaten::count();
        $lastCol  = 'O';
        $totCol   = 'O';
        $r1 = 1; $r2 = 4;
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $dS = 6; $dE = $dS + $nKab - 1;
        $fR = $dE + 1; $nR = $fR + 2;

        // Merge header
        $sheet->mergeCells("A{$r1}:{$lastCol}{$r1}");   // judul full
        $sheet->mergeCells("A2:{$lastCol}2");           // subtitle full
        $sheet->mergeCells("A3:{$lastCol}3");           // spacer full
        $sheet->mergeCells("A4:A5");
        $sheet->mergeCells("B4:B5");
        $sheet->mergeCells("C4:N4");
        $sheet->mergeCells("O4:O5");

        // Row 1 – JUDUL: font gelap, NO background fill
        $sheet->getStyle("A{$r1}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>14,'color'=>['rgb'=>'111827'],'name'=>'Arial'],
            'fill'      => ['fillType'=>Fill::FILL_NONE],   // TIDAK ADA BACKGROUND
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($r1)->setRowHeight(26);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold'=>true,'size'=>11,'color'=>['rgb'=>'374151'],'name'=>'Arial'],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getStyle("A{$r2}:{$lastCol}{$r2}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::ORANGE_600]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::ORANGE_700]]],
        ]);
        $sheet->getRowDimension($r2)->setRowHeight(26);
        $sheet->getStyle("C5:N5")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::ORANGE_500]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::ORANGE_700]]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(18);

        // Data rows — border TEGAS di semua sel
        $borderThin  = ['borderStyle'=>Border::BORDER_THIN, 'color'=>['rgb'=>'E5E7EB']];

        for ($r = $dS; $r <= $dE; $r++) {
            $bg = (($r-$dS) % 2 === 0) ? self::WHITE : self::GRAY_100;
            // Set background & border SELURUH baris
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'fill'    => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
                'borders' => ['allBorders' => $borderThin],
                'font'    => ['name'=>'Arial','size'=>9,'color'=>['rgb'=>'111827']],
            ]);
            // No
            $sheet->getStyle("A{$r}")->applyFromArray([
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
                'font'      => ['bold'=>true],
            ]);
            // Kabupaten
            $sheet->getStyle("B{$r}")->applyFromArray([
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_LEFT],
                'font'      => ['bold'=>true],
            ]);
            // Data bulan C-N
            $sheet->getStyle("C{$r}:N{$r}")->applyFromArray([
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
            ]);
            // Total col O — highlight
            $sheet->getStyle("{$totCol}{$r}")->applyFromArray([
                'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::ORANGE_100]],
                'font'      => ['bold'=>true,'color'=>['rgb'=>self::ORANGE_900],'name'=>'Arial','size'=>9],
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
                'borders'   => ['allBorders' => $borderThin],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // Footer SUMATERA SELATAN
        $sheet->mergeCells("A{$fR}:B{$fR}");
        $sheet->getStyle("A{$fR}:{$lastCol}{$fR}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>10,'color'=>['rgb'=>self::WHITE],'name'=>'Arial'],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::ORANGE_700]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::ORANGE_800]]],
        ]);
        $sheet->getStyle("C{$fR}:N{$fR}")->applyFromArray([
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getStyle("{$totCol}{$fR}")->applyFromArray([
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::ORANGE_800]],
            'font'      => ['bold'=>true,'color'=>['rgb'=>self::WHITE],'name'=>'Arial','size'=>10],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getRowDimension($fR)->setRowHeight(22);

        // Note
        $sheet->getStyle("A{$nR}")->applyFromArray([
            'font' => ['italic'=>true,'size'=>8,'color'=>['rgb'=>'9CA3AF']],
        ]);

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
        return [
            'A'=>4,'B'=>26,
            'C'=>8,'D'=>8,'E'=>8,'F'=>8,'G'=>8,'H'=>8,
            'I'=>8,'J'=>8,'K'=>8,'L'=>8,'M'=>8,'N'=>8,
            'O'=>13,
        ];
    }
}