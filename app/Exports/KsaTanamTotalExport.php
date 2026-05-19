<?php

namespace App\Exports;

use App\Models\KsaLuasTanam;
use App\Models\Kabupaten;
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

class KsaTanamTotalExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    const C600  = 'EA580C';
    const C700  = 'C2410C';
    const C400  = 'FB923C';
    const C800  = '9A3412';
    const C900  = '7C2D12';
    const C50   = 'FFF7ED';
    const C100  = 'FFEDD5';
    const WHITE = 'FFFFFF';
    const FMT   = '#,##0.##';

    protected int $tahunAwal;
    protected int $tahunAkhir;

    public function __construct(int $tahunAwal, int $tahunAkhir)
    {
        $this->tahunAwal  = $tahunAwal;
        $this->tahunAkhir = $tahunAkhir;
    }

    public function title(): string
    {
        return "Tanam Total {$this->tahunAwal}-{$this->tahunAkhir}";
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
        $years = range($this->tahunAwal, $this->tahunAkhir);
        $nY    = count($years);
        $kabs  = Kabupaten::orderBy('id')->get();

        // ── Hitung total per tahun (periode Okt–Sep) ──
        $totalPT = [];
        foreach ($years as $pt) {
            $totalPT[$pt] = (float) KsaLuasTanam::where(function ($q) use ($pt) {
                $q->where(function ($q2) use ($pt) {
                    $q2->where('tahun', $pt)->whereIn('bulan', [10, 11, 12]);
                })->orWhere(function ($q2) use ($pt) {
                    $q2->where('tahun', $pt + 1)->whereBetween('bulan', [1, 9]);
                });
            })->sum('luas_tanam');
        }

        $out = [];

        // ── Row 1: Judul ──
        $out[] = array_merge(
            ['KSA LTT PADI — SANDING TOTAL TAHUNAN (PERIODE OKT–SEP)'],
            array_fill(0, $nY + 2, '')
        );

        // ── Row 2: Subtitle ──
        $out[] = array_merge(
            ["Periode: Oktober {$this->tahunAwal} – September {$this->tahunAkhir}   |   Satuan: Hektar (Ha)"],
            array_fill(0, $nY + 2, '')
        );

        // ── Row 3: Kosong ──
        $out[] = array_fill(0, $nY + 3, '');

        // ── Row 4: Header baris 1 ──
        $header1 = ['No', 'Kabupaten/Kota', 'Total LTT Padi'];
        for ($i = 1; $i < $nY; $i++) {
            $header1[] = '';
        }
        $header1[] = 'Total';
        $out[] = $header1;

        // ── Row 5: Header baris 2 (sub-header tahun) ──
        $lbl = ['', ''];
        foreach ($years as $y) {
            $lbl[] = "Okt {$y}";
        }
        $lbl[] = '';
        $out[] = $lbl;

        // ── Rows data per kabupaten ──
        $no = 1;
        foreach ($kabs as $kab) {
            $row = [$no++, strtoupper($kab->nama_kabupaten)];
            $kT  = 0;
            foreach ($years as $pt) {
                $v = (float) KsaLuasTanam::where('kabupaten_id', $kab->id)
                    ->where(function ($q) use ($pt) {
                        $q->where(function ($q2) use ($pt) {
                            $q2->where('tahun', $pt)->whereIn('bulan', [10, 11, 12]);
                        })->orWhere(function ($q2) use ($pt) {
                            $q2->where('tahun', $pt + 1)->whereBetween('bulan', [1, 9]);
                        });
                    })->sum('luas_tanam');
                $row[] = $this->formatData($v);
                $kT   += $v;
            }
            $row[] = $this->formatData($kT);
            $out[] = $row;
        }

        // ── Row footer: Total Sumatera Selatan ──
        $footer = ['SUMATERA SELATAN', null];
        $grand  = 0;
        foreach ($years as $y) {
            $v        = $totalPT[$y] ?? 0;
            $footer[] = $this->formatData($v);
            $grand   += $v;
        }
        $footer[] = $this->formatData($grand);
        $out[]    = $footer;

        // ── Catatan kaki ──
        $out[] = [];
        $out[] = ['* Satuan: Hektar (Ha)   |   Nilai = SUM LTT Oktober–September tiap tahun'];

        return $out;
    }

    public function styles(Worksheet $sheet): array
    {
        $years    = range($this->tahunAwal, $this->tahunAkhir);
        $nY       = count($years);
        $lastColI = $nY + 3;
        $lastCol  = Coordinate::stringFromColumnIndex($lastColI);
        $dataEndC = Coordinate::stringFromColumnIndex($lastColI - 1);
        $totCol   = $lastCol;
        $nKab     = Kabupaten::count();

        $r1  = 1;
        $r2  = 4;
        $r3  = 5;
        $dS  = 6;
        $dE  = $dS + $nKab - 1;
        $fR  = $dE + 1;
        $nR  = $fR + 2;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

        // ── Merge judul & subtitle ──
        $sheet->mergeCells("A{$r1}:{$lastCol}{$r1}");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        $sheet->getStyle("A{$r1}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '111827'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_NONE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151'], 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($r1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(15);

        // ── Merge header ──
        $sheet->mergeCells("A{$r2}:A{$r3}");
        $sheet->mergeCells("B{$r2}:B{$r3}");
        $sheet->mergeCells("{$totCol}{$r2}:{$totCol}{$r3}");
        if ($nY > 1) {
            $sheet->mergeCells("C{$r2}:{$dataEndC}{$r2}");
        }

        $sheet->getStyle("A{$r2}:{$lastCol}{$r2}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C600]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C700]]],
        ]);
        $sheet->getRowDimension($r2)->setRowHeight(26);

        $sheet->getStyle("A{$r3}:{$lastCol}{$r3}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F97316']], // ORANGE_500
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C700]]],
        ]);
        $sheet->getRowDimension($r3)->setRowHeight(18);

        // ── Style rows data ──
        $bt = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']];

        for ($r = $dS; $r <= $dE; $r++) {
            $bg = (($r - $dS) % 2 === 0) ? self::WHITE : 'F9FAFB'; // GRAY-50
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => $bt],
                'font'    => ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => '111827']],
            ]);
            $sheet->getStyle("A{$r}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font'      => ['bold' => true, 'color' => ['rgb' => '4B5563']],
            ]);
            $sheet->getStyle("B{$r}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'font'      => ['bold' => true, 'color' => ['rgb' => '111827']],
            ]);
            for ($c = 3; $c <= $lastColI; $c++) {
                $cl = Coordinate::stringFromColumnIndex($c);
                $sheet->getStyle("{$cl}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("{$totCol}{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C100]],
                'font' => ['bold' => true, 'color' => ['rgb' => self::C900]],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // ── Footer row ──
        $sheet->mergeCells("A{$fR}:B{$fR}");
        $sheet->getStyle("A{$fR}:{$lastCol}{$fR}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C700]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C800]]],
        ]);
        $sheet->getStyle("A{$fR}:B{$fR}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

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
        $w = ['A' => 4, 'B' => 26];
        foreach (range($this->tahunAwal, $this->tahunAkhir) as $i => $y) {
            $w[Coordinate::stringFromColumnIndex($i + 3)] = 11;
        }
        $nY       = $this->tahunAkhir - $this->tahunAwal + 1;
        $w[Coordinate::stringFromColumnIndex($nY + 3)] = 13;
        return $w;
    }
}