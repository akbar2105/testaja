<?php

namespace App\Exports;

use App\Models\KsaProduksi;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\DB;
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

class KsaProduksiBulananExport implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    protected int $tahun;

    // Blue-indigo palette (sesuai blade produksi)
    const COLOR_HEADER_BG   = '2563EB'; // blue-600
    const COLOR_SUBHDR_BG   = '1D4ED8'; // blue-700
    const COLOR_FOOTER_BG   = '1E40AF'; // blue-800
    const COLOR_FOOTER_CELL = '1E3A8A'; // blue-900
    const COLOR_WHITE       = 'FFFFFF';
    const COLOR_GRAY_ROW    = 'F9FAFB';
    const COLOR_TOTAL_BG    = 'EFF6FF'; // blue-50
    const COLOR_TOTAL_TEXT  = '1D4ED8'; // blue-700

    public function __construct(int $tahun)
    {
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return "Produksi {$this->tahun}";
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
        $tahun      = $this->tahun;
        $bulanMap   = KsaProduksi::getBulanList(); // [1=>'Januari',...,12=>'Desember']
        $kabupatens = Kabupaten::orderBy('id')->get();

        $records = KsaProduksi::where('tahun', $tahun)->get()->groupBy('kabupaten_id');

        $totalPerBulan = KsaProduksi::where('tahun', $tahun)
            ->select('bulan', DB::raw('SUM(produksi) as total'))
            ->groupBy('bulan')->pluck('total', 'bulan');

        $rows = [];
        $rows[] = ["KSA PRODUKSI PADI (TON GKG) — SANDING BULANAN JANUARI–DESEMBER"];
        $rows[] = ["Tahun: {$tahun}   |   Satuan: Ton GKG"];
        $rows[] = array_fill(0, 15, '');

        $rows[] = array_merge(['No', 'Kabupaten/Kota', 'Produksi Padi (Ton GKG) Tahun '.$tahun], array_fill(0, 11, ''), ['Total']);
        $bulanRow = ['', ''];
        foreach ($bulanMap as $nama) $bulanRow[] = substr($nama, 0, 3);
        $bulanRow[] = '';
        $rows[] = $bulanRow;

        foreach ($kabupatens as $i => $kab) {
            $kabRows  = $records->get($kab->id, collect());
            $row      = [$i + 1, $kab->nama_kabupaten];
            $totalKab = 0;
            foreach ($bulanMap as $b => $nama) {
                $val = $kabRows->firstWhere('bulan', $b)?->produksi ?? 0;
                $row[] = $this->formatData($val);
                $totalKab += (float) $val;
            }
            $row[] = $this->formatData($totalKab);
            $rows[] = $row;
        }

        $footer     = ['', 'SUMATERA SELATAN'];
        $grandTotal = 0;
        foreach ($bulanMap as $b => $nama) {
            $tot = (float) ($totalPerBulan->get($b, 0));
            $footer[] = $this->formatData($tot);
            $grandTotal += $tot;
        }
        $footer[] = $this->formatData($grandTotal);
        $rows[] = $footer;

        $rows[] = [];
        $rows[] = ["* Satuan: Ton GKG   |   Tahun: {$tahun}"];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $bulanCount  = 12;
        $lastColIdx  = $bulanCount + 3;
        $lastColLtr  = Coordinate::stringFromColumnIndex($lastColIdx);
        $totalColLtr = $lastColLtr;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

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
        $sheet->mergeCells('C4:N4');
        $sheet->mergeCells('O4:O5');

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
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E40AF']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getStyle("C5:N5")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_WHITE], 'size' => 10, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_SUBHDR_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E40AF']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(18);

        for ($r = $dataRowStart; $r <= $dataRowEnd; $r++) {
            $bgColor = ($r % 2 === 1) ? self::COLOR_GRAY_ROW : self::COLOR_WHITE;
            $sheet->getStyle("A{$r}:{$lastColLtr}{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'font'    => ['size' => 9, 'name' => 'Arial'],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(15);
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
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
            ]);
        }

        $sheet->getStyle("A{$footerRow}:{$lastColLtr}{$footerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_WHITE], 'size' => 10, 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_FOOTER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E3A8A']]],
        ]);
        $sheet->getRowDimension($footerRow)->setRowHeight(21.6);
        $sheet->getStyle("A{$footerRow}:B{$footerRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("{$totalColLtr}{$footerRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_FOOTER_CELL]],
        ]);

        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '9CA3AF'], 'name' => 'Arial'],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getRowDimension(4)->setRowHeight(26);
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
        return [
            'A' => 5,  'B' => 22,
            'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10,
            'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10,
            'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10,
            'O' => 14,
        ];
    }
}