<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

use App\Models\LuasBakuSawah;
use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
class LuasBakuSawahExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $years;
    protected $rowNumber = 0;

    public function __construct($years)
    {
        $this->years = $years;
        sort($this->years);
    }

    public function collection()
    {
        $kabupatens = Kabupaten::orderBy('id')->get();
        $result     = collect();

        foreach ($kabupatens as $kab) {
            $row = ['kabupaten' => $kab, 'years' => []];
            foreach ($this->years as $year) {
                $lbs                 = LuasBakuSawah::where('kabupaten_id', $kab->id)->where('tahun', $year)->first();
                $row['years'][$year] = $lbs ? $lbs->luas_baku_sawah : 0;
            }
            $row['total'] = array_sum($row['years']);
            $result->push((object) $row);
        }

        return $result;
    }

    public function headings(): array
    {
        $yearCount   = count($this->years);
        $yearDisplay = $this->formatYearDisplay();

        return [
            ['Sanding Luas Baku Sawah ' . $yearDisplay],
            [],
            array_merge(['No', 'Kabupaten/Kota'], array_fill(0, $yearCount, 'Luas Baku Sawah'), ['TOTAL']),
            array_merge(['', ''], $this->years, ['']),
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $data = [$this->rowNumber, strtoupper($row->kabupaten->nama_kabupaten)];
        foreach ($this->years as $year) {
            $data[] = $row->years[$year] ?? 0;
        }
        $data[] = $row->total;
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

        $yearCount = count($this->years);
        $lastColI  = 2 + $yearCount + 1; // +1 for TOTAL
        $lastCol   = Coordinate::stringFromColumnIndex($lastColI);

        $lastRow  = $this->rowNumber + 4; // 4 baris header
        $totalRow = $lastRow + 1;

        // ── Judul ──────────────────────────────────────────────────────────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold'=>true,'size'=>16,'color'=>['argb'=>'FF0D9488']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Header No & Kabupaten (merge 2 baris) ──────────────────────────
        $sheet->mergeCells('A3:A4');
        $sheet->getStyle('A3:A4')->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['argb'=>'FFFFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FF14B8A6']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['argb'=>'FF0D9488']]],
        ]);

        $sheet->mergeCells('B3:B4');
        $sheet->getStyle('B3:B4')->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['argb'=>'FFFFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FF14B8A6']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['argb'=>'FF0D9488']]],
        ]);

        // ── Header data cols baris 3 (merge C s.d. lastCol) ───────────────
        if ($yearCount >= 1) {
            $prevCol = Coordinate::stringFromColumnIndex($lastColI - 1);
            $sheet->mergeCells("C3:{$prevCol}3");
        }
        $sheet->mergeCells("{$lastCol}3:{$lastCol}4");

        $sheet->getStyle("C3:{$lastCol}4")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['argb'=>'FFFFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FF14B8A6']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['argb'=>'FF0D9488']]],
        ]);

        // ── Header baris 4 (label tahun) ───────────────────────────────────
        $sheet->getStyle("C4:{$prevCol}4")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['argb'=>'FF0F172A']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FF5EEAD4']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['argb'=>'FF14B8A6']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // ── Data rows ─────────────────────────────────────────────────────
        $sheet->getStyle("A5:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['argb'=>'FFD1D5DB']]],
            'font'    => ['size'=>9],
        ]);
        $sheet->getStyle("C5:{$lastCol}{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getStyle("A5:A{$lastRow}")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'4B5563']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("B5:B{$lastRow}")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'111827']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_LEFT],
        ]);

        // Zebra rows
        for ($r = 5; $r <= $lastRow; $r++) {
            $bg = ($r % 2 === 1) ? 'F9FAFB' : 'FFFFFF';
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
            $sheet->getStyle("{$lastCol}{$r}")->applyFromArray([
                'font' => ['bold'=>true],
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FFFFF0']],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // ── Footer JUMLAH — merge A:B ──────────────────────────────────────
        $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'JUMLAH');

        // Formula SUM tiap kolom data
        for ($ci = 3; $ci <= $lastColI; $ci++) {
            $col = Coordinate::stringFromColumnIndex($ci);
            $sheet->setCellValue("{$col}{$totalRow}", "=SUM({$col}5:{$col}{$lastRow})");
        }

        $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>11,'color'=>['argb'=>'FFFFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FF0D9488']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_MEDIUM,'color'=>['argb'=>'FF0F766E']]],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getRowDimension($totalRow)->setRowHeight(22);

        $this->applyNumberFormat($sheet, "C5:{$lastCol}{$totalRow}");

        // ── Keterangan ────────────────────────────────────────────────────
        $noteRow = $totalRow + 1;
        $sheet->mergeCells("A{$noteRow}:{$lastCol}{$noteRow}");
        $sheet->setCellValue("A{$noteRow}", '* Satuan: Hektar (Ha)  |  Tahun: ' . $this->formatYearDisplay());
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font'      => ['size'=>11,'color'=>['argb'=>'FF000000']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_LEFT],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'LBS ' . $this->formatYearDisplay();
    }

    public function columnWidths(): array
    {
        $widths = ['A'=>5, 'B'=>28];
        foreach ($this->years as $index => $year) {
            $widths[Coordinate::stringFromColumnIndex(3 + $index)] = 15;
        }
        $widths[Coordinate::stringFromColumnIndex(3 + count($this->years))] = 18;
        return $widths;
    }

    /* ── Helper ─────────────────────────────────────────────────────────── */
    private function formatYearDisplay(): string
    {
        $n = count($this->years);
        if ($n === 1) return (string) $this->years[0];
        if ($n === 2) return $this->years[0] . ' dan ' . $this->years[1];
        if ($n === 3) return $this->years[0] . ', ' . $this->years[1] . ', dan ' . $this->years[2];
        return min($this->years) . '–' . max($this->years);
    }

    private function applyNumberFormat(object $sheet, string $range): void
    {
        $boundaries = Coordinate::rangeBoundaries($range);
        [$cStart, $rStart] = $boundaries[0];
        [$cEnd,   $rEnd  ] = $boundaries[1];

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
    }
}