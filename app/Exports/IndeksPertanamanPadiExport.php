<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use App\Models\IndeksPertanamanPadi;
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

class IndeksPertanamanPadiExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $years;
    protected $rowNumber = 0;
    protected $totalKab  = 0;

    public function __construct($years)
    {
        $this->years    = $years;
        $this->totalKab = Kabupaten::count(); // ← hitung di constructor agar styles() bisa pakai
        sort($this->years);
    }

    public function collection()
    {
        $kabupatens     = Kabupaten::orderBy('id')->get();
        $this->totalKab = $kabupatens->count(); // update tepat
        $result         = collect();

        foreach ($kabupatens as $kab) {
            $row = ['kabupaten' => $kab, 'years' => []];
            foreach ($this->years as $year) {
                $ip                  = IndeksPertanamanPadi::where('kabupaten_id', $kab->id)
                                            ->where('tahun', $year)->first();
                $row['years'][$year] = $ip ? (float) $ip->ip : 0.00;
            }
            $row['total'] = array_sum($row['years']);
            $result->push((object) $row);
        }

        return $result;
    }

    public function headings(): array
    {
        $yearCount = count($this->years);
        return [
            // Baris 1 – judul
            ['Indeks Pertanaman (IP) Tanaman Padi ' . $this->formatYearDisplay()],
            // Baris 2 – kosong
            [],
            // Baris 3 – header level-1
            array_merge(['No', 'Kabupaten/Kota'], array_fill(0, $yearCount, 'Indeks Pertanaman Padi'), ['TOTAL']),
            // Baris 4 – sub-header tahun
            array_merge(['', ''], $this->years, ['']),
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $data = [$this->rowNumber, strtoupper($row->kabupaten->nama_kabupaten)];
        foreach ($this->years as $year) {
            $data[] = isset($row->years[$year]) ? (float) $row->years[$year] : 0.00;
        }
        $data[] = (float) $row->total;
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

        $yearCount    = count($this->years);
        $lastColI     = 2 + $yearCount + 1;
        $lastCol      = Coordinate::stringFromColumnIndex($lastColI);
        $prevCol      = Coordinate::stringFromColumnIndex($lastColI - 1);

        // ── Perhitungan baris yang BENAR ───────────────────────────────────
        // headings() menghasilkan 4 baris → data mulai baris 5
        // jumlah baris data = $totalKab (tidak bergantung pada $rowNumber
        // yang bisa saja 0 jika styles() dipanggil sebelum map())
        $dataStart = 5;
        $lastRow   = 4 + $this->totalKab;   // mis. 17 kab → lastRow = 21
        $totalRow  = $lastRow + 1;           // baris Sumatera Selatan

        // ── Judul ──────────────────────────────────────────────────────────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Header No (A3:A4) ──────────────────────────────────────────────
        $sheet->mergeCells('A3:A4');
        $sheet->getStyle('A3:A4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF22C55E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF15803D']]],
        ]);

        // ── Header Kabupaten/Kota (B3:B4) ─────────────────────────────────
        $sheet->mergeCells('B3:B4');
        $sheet->getStyle('B3:B4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF22C55E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF15803D']]],
        ]);

        // ── Header kolom data tahun (C3 s.d. prevCol) ─────────────────────
        if ($yearCount >= 1) {
            $sheet->mergeCells("C3:{$prevCol}3");
        }

        // ── Header TOTAL (merge 2 baris) ───────────────────────────────────
        $sheet->mergeCells("{$lastCol}3:{$lastCol}4");

        $sheet->getStyle("C3:{$lastCol}4")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF22C55E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF15803D']]],
        ]);

        // ── Sub-header tahun (baris 4) ─────────────────────────────────────
        $sheet->getStyle("C4:{$prevCol}4")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FF0F172A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF86EFAC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF22C55E']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // ── Gaya baris data ────────────────────────────────────────────────
        $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
            'font'    => ['size' => 9],
        ]);
        $sheet->getStyle("C{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getStyle("A{$dataStart}:A{$lastRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("B{$dataStart}:B{$lastRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '111827']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // ── Zebra striping ─────────────────────────────────────────────────
        for ($r = $dataStart; $r <= $lastRow; $r++) {
            $bg = ($r % 2 === 1) ? 'F9FAFB' : 'FFFFFF';
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
            $sheet->getStyle("{$lastCol}{$r}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF0']],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // ── Baris SUMATERA SELATAN ─────────────────────────────────────────
        $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'SUMATERA SELATAN');

        // SUM tiap kolom data (C s.d. lastCol)
        for ($ci = 3; $ci <= $lastColI; $ci++) {
            $col = Coordinate::stringFromColumnIndex($ci);
            $sheet->setCellValue(
                "{$col}{$totalRow}",
                "=SUM({$col}{$dataStart}:{$col}{$lastRow})"
            );
        }

        $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF166534']]],
        ]);
        $sheet->getStyle("C{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getRowDimension($totalRow)->setRowHeight(22);

        // ── Format angka 2 desimal semua sel data + baris Sumatera Selatan ─
        $this->applyNumberFormat($sheet, "C{$dataStart}:{$lastCol}{$totalRow}");

        // ── Keterangan ────────────────────────────────────────────────────
        $noteRow = $totalRow + 1;
        $sheet->mergeCells("A{$noteRow}:{$lastCol}{$noteRow}");
        $sheet->setCellValue(
            "A{$noteRow}",
            '* Satuan: Indeks (kali tanam per tahun)  |  Tahun: ' . $this->formatYearDisplay()
        );
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font'      => ['size' => 11, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'IP Padi ' . $this->formatYearDisplay();
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 5, 'B' => 28];
        foreach ($this->years as $index => $year) {
            $widths[Coordinate::stringFromColumnIndex(3 + $index)] = 15;
        }
        $widths[Coordinate::stringFromColumnIndex(3 + count($this->years))] = 18;
        return $widths;
    }

    /* ── Helpers ─────────────────────────────────────────────────────────── */

    private function formatYearDisplay(): string
    {
        $n = count($this->years);
        if ($n === 1) return (string) $this->years[0];
        if ($n === 2) return $this->years[0] . ' dan ' . $this->years[1];
        if ($n === 3) return $this->years[0] . ', ' . $this->years[1] . ', dan ' . $this->years[2];
        return min($this->years) . '–' . max($this->years);
    }

    /**
     * Format angka 2 desimal (#,##0.00) untuk seluruh range.
     * Sel kosong/null → 0 numerik.
     * Sel berisi formula → format diterapkan, nilai TIDAK disentuh.
     */
    private function applyNumberFormat(object $sheet, string $range): void
    {
        $sheet->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00;-#,##0.00;"0.00"');

        $boundaries = Coordinate::rangeBoundaries($range);
        [$cStart, $rStart] = $boundaries[0];
        [$cEnd,   $rEnd  ] = $boundaries[1];

        for ($r = $rStart; $r <= $rEnd; $r++) {
            for ($c = $cStart; $c <= $cEnd; $c++) {
                $cell  = $sheet->getCellByColumnAndRow($c, $r);
                $value = $cell->getValue();

                // Lewati formula
                if (is_string($value) && str_starts_with($value, '=')) {
                    continue;
                }

                // Paksa 0 numeric untuk sel kosong/null
                if ($value === null || $value === '') {
                    $cell->setValueExplicit(0, DataType::TYPE_NUMERIC);
                }
            }
        }
    }
}