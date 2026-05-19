<?php

namespace App\Exports;

use App\Models\KsaLuasPanen;
use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class KsaPanenTahunanExport implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    protected int $bulan;
    protected int $tahunAwal;
    protected int $tahunAkhir;

    // Violet palette (sama dengan panen bulanan)
    const COLOR_HEADER_BG   = '7C3AED';
    const COLOR_SUBHDR_BG   = '8B5CF6';
    const COLOR_FOOTER_BG   = '5B21B6';
    const COLOR_FOOTER_CELL = '4C1D95';
    const COLOR_WHITE       = 'FFFFFF';
    const COLOR_GRAY_ROW    = 'F9FAFB';
    const COLOR_TOTAL_BG    = 'EDE9FE';
    const COLOR_TOTAL_TEXT  = '6D28D9';

    public function __construct(int $bulan, int $tahunAwal, int $tahunAkhir)
    {
        $this->bulan      = $bulan;
        $this->tahunAwal  = $tahunAwal;
        $this->tahunAkhir = $tahunAkhir;
    }

    public function title(): string
    {
        $namaBulan = KsaLuasPanen::getNamaBulan($this->bulan);
        return "{$namaBulan} {$this->tahunAwal}-{$this->tahunAkhir}";
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
        $bulan      = $this->bulan;
        $tahunAwal  = $this->tahunAwal;
        $tahunAkhir = $this->tahunAkhir;
        $namaBulan  = KsaLuasPanen::getNamaBulan($bulan);
        $years      = range($tahunAwal, $tahunAkhir);
        $kabupatens = Kabupaten::orderBy('id')->get();

        $rawRows = KsaLuasPanen::where('bulan', $bulan)
            ->whereIn('tahun', $years)->get()->groupBy('kabupaten_id');

        $rows = [];
        $rows[] = array_merge(["KSA LUAS PANEN PADI (HEKTAR) — SANDING TAHUNAN PER BULAN"], array_fill(0, count($years) + 2, ''));
        $rows[] = array_merge(["Bulan: {$namaBulan}   |   Periode: {$tahunAwal}–{$tahunAkhir}   |   Satuan: Hektar (Ha)"], array_fill(0, count($years) + 2, ''));
        $rows[] = array_fill(0, count($years) + 3, '');

        $header1 = ['No', 'Kabupaten/Kota', "Luas Panen (Ha) — {$namaBulan}"];
        for ($i = 1; $i < count($years); $i++) $header1[] = '';
        $header1[] = 'Total';
        $rows[] = $header1;

        $header2 = ['', ''];
        foreach ($years as $y) $header2[] = (string) $y;
        $header2[] = '';
        $rows[] = $header2;

        $totals = array_fill_keys($years, 0.0);
        foreach ($kabupatens as $i => $kab) {
            $kabRecs  = $rawRows->get($kab->id, collect());
            $row      = [$i + 1, $kab->nama_kabupaten];
            $kabTotal = 0;
            foreach ($years as $y) {
                $val = (float) ($kabRecs->firstWhere('tahun', $y)?->luas_panen ?? 0);
                $row[] = $this->formatData($val);
                $totals[$y] += $val;
                $kabTotal   += $val;
            }
            $row[] = $this->formatData($kabTotal);
            $rows[] = $row;
        }

        $footer     = ['SUMATERA SELATEN', null];
        $grandTotal = 0;
        foreach ($years as $y) {
            $footer[] = $this->formatData($totals[$y]);
            $grandTotal += $totals[$y];
        }
        $footer[] = $this->formatData($grandTotal);
        $rows[] = $footer;

        $rows[] = [];
        $rows[] = ["* Satuan: Hektar (Ha)   |   Bulan: {$namaBulan}   |   Tahun: {$tahunAwal}–{$tahunAkhir}"];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $yearCount      = $this->tahunAkhir - $this->tahunAwal + 1;
        $lastColIdx     = 2 + $yearCount + 1;
        $lastDataColIdx = $lastColIdx - 1;
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $lastColLtr  = Coordinate::stringFromColumnIndex($lastColIdx);
        $lastDataLtr = Coordinate::stringFromColumnIndex($lastDataColIdx);
        $totalColLtr = $lastColLtr;

        $kabupatens   = Kabupaten::orderBy('id')->get();
        $dataRowStart = 6;
        $dataRowEnd   = $dataRowStart + $kabupatens->count() - 1;
        $footerRow    = $dataRowEnd + 1;
        $noteRow      = $footerRow + 2;

        $sheet->mergeCells("A1:{$lastColLtr}1");
        $sheet->mergeCells("A2:{$lastColLtr}2");
        $sheet->mergeCells("A3:{$lastColLtr}3");
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells("C4:{$lastDataLtr}4");
        $sheet->mergeCells("{$totalColLtr}4:{$totalColLtr}5");

        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '111827'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_NONE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_NONE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("A4:{$lastColLtr}4")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_WHITE], 'size' => 10, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '5B21B6']]],
        ]);
        $sheet->getStyle("C5:{$lastDataLtr}5")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SUBHDR_BG]],
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_WHITE], 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '5B21B6']]],
        ]);

        for ($r = $dataRowStart; $r <= $dataRowEnd; $r++) {
            $bgColor = (($r - $dataRowStart) % 2 === 0) ? self::COLOR_WHITE : self::COLOR_GRAY_ROW;
            $sheet->getStyle("A{$r}:{$lastColLtr}{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'font'    => ['size' => 9, 'name' => 'Arial'],
            ]);
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => '4B5563'], 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("B{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => '111827'], 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
            for ($c = 3; $c <= $lastColIdx; $c++) {
                $cLtr = Coordinate::stringFromColumnIndex($c);
                $sheet->getStyle("{$cLtr}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $sheet->getStyle("{$totalColLtr}{$r}")->applyFromArray([
                'font'    => ['bold' => true, 'color' => ['rgb' => self::COLOR_TOTAL_TEXT], 'name' => 'Arial'],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_TOTAL_BG]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDD6FE']]],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        $sheet->mergeCells("A{$footerRow}:B{$footerRow}");
        $sheet->getStyle("A{$footerRow}:{$lastColLtr}{$footerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_WHITE], 'size' => 10, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_FOOTER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4C1D95']]],
        ]);
        $sheet->getStyle("A{$footerRow}:B{$footerRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("{$totalColLtr}{$footerRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_FOOTER_CELL]],
        ]);
        $sheet->getRowDimension($footerRow)->setRowHeight(22);

        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '9CA3AF'], 'name' => 'Arial'],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getRowDimension(5)->setRowHeight(18);
        $sheet->freezePane('A6');

        $lastLet = isset($lastColLtr) ? $lastColLtr : (isset($lastCol) ? $lastCol : 'O');
        $range = "C" . $dataRowStart . ":" . $lastLet . $footerRow;
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
        $widths = ['A' => 5, 'B' => 22];
        $years  = range($this->tahunAwal, $this->tahunAkhir);
        $idx    = 3;
        foreach ($years as $y) {
            $ltr          = Coordinate::stringFromColumnIndex($idx++);
            $widths[$ltr] = 12;
        }
        $ltr          = Coordinate::stringFromColumnIndex($idx);
        $widths[$ltr] = 14;
        return $widths;
    }
}