<?php

namespace App\Exports\Style;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait KsaExportStyle
{
    // ── ORANGE — KSA TANAM ─────────────────────────────────────────────────
    // blade: from-orange-500/from-orange-400  tfoot: orange-700/orange-800
    const TANAM_TITLE_BG = 'FFF7ED';
    const TANAM_SUB_BG   = 'FFEDD5';
    const TANAM_H1_BG    = 'F97316'; // orange-500  (thead row-1)
    const TANAM_H2_BG    = 'FB923C'; // orange-400  (thead row-2)
    const TANAM_FOOT_BG  = 'C2410C'; // orange-700  (tfoot)
    const TANAM_STRIPE   = 'FFF7ED'; // orange-50
    const TANAM_TEXT     = '431407'; // orange-950
    const TANAM_BORDER_H = 'EA580C'; // orange-600  (header border)
    const TANAM_BORDER_D = 'E5E7EB'; // gray-200    (data border)

    // ── VIOLET-INDIGO — KSA PANEN ──────────────────────────────────────────
    // blade: from-violet-500/from-violet-400  tfoot: violet-600/indigo-600
    const PANEN_TITLE_BG = 'F5F3FF';
    const PANEN_SUB_BG   = 'EDE9FE';
    const PANEN_H1_BG    = '7C3AED'; // violet-600
    const PANEN_H2_BG    = '8B5CF6'; // violet-500
    const PANEN_FOOT_BG  = '3730A3'; // indigo-800
    const PANEN_STRIPE   = 'F5F3FF';
    const PANEN_TEXT     = '2E1065';
    const PANEN_BORDER_H = '6D28D9'; // violet-700
    const PANEN_BORDER_D = 'E5E7EB';

    // ── BLUE-INDIGO — KSA PRODUKSI ─────────────────────────────────────────
    // blade: from-blue-600/from-blue-500  tfoot: blue-700/indigo-700
    const PROD_TITLE_BG  = 'EFF6FF';
    const PROD_SUB_BG    = 'DBEAFE';
    const PROD_H1_BG     = '1D4ED8'; // blue-700
    const PROD_H2_BG     = '3B82F6'; // blue-500
    const PROD_FOOT_BG   = '1E3A8A'; // blue-900
    const PROD_STRIPE    = 'EFF6FF';
    const PROD_TEXT      = '1E3A8A';
    const PROD_BORDER_H  = '1D4ED8';
    const PROD_BORDER_D  = 'E5E7EB';

    const FMT_NUM = '#,##0.00';

    // ─────────────────────────────────────────────────────────────────────────
    // THEME RESOLVER — returns [titleBg, subBg, h1, h2, foot, stripe, text, borderH, borderD]
    // ─────────────────────────────────────────────────────────────────────────
    private function tc(string $theme): array
    {
        return match ($theme) {
            'panen'    => [self::PANEN_TITLE_BG, self::PANEN_SUB_BG, self::PANEN_H1_BG, self::PANEN_H2_BG, self::PANEN_FOOT_BG, self::PANEN_STRIPE, self::PANEN_TEXT, self::PANEN_BORDER_H, self::PANEN_BORDER_D],
            'produksi' => [self::PROD_TITLE_BG,  self::PROD_SUB_BG,  self::PROD_H1_BG,  self::PROD_H2_BG,  self::PROD_FOOT_BG,  self::PROD_STRIPE,  self::PROD_TEXT,  self::PROD_BORDER_H,  self::PROD_BORDER_D],
            default    => [self::TANAM_TITLE_BG,  self::TANAM_SUB_BG, self::TANAM_H1_BG, self::TANAM_H2_BG, self::TANAM_FOOT_BG, self::TANAM_STRIPE, self::TANAM_TEXT, self::TANAM_BORDER_H, self::TANAM_BORDER_D],
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STYLE ARRAYS
    // ─────────────────────────────────────────────────────────────────────────
    protected function styleRowTitle(string $t): array
    {
        [$tbg,, $h1,,,, $text, $bh] = $this->tc($t);
        return [
            'font'      => ['bold' => true, 'size' => 13, 'name' => 'Arial', 'color' => ['rgb' => $text]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $tbg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $bh]]],
        ];
    }

    protected function styleRowSub(string $t): array
    {
        [, $sbg,,,,, $text, $bh] = $this->tc($t);
        return [
            'font'      => ['italic' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['rgb' => $text]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sbg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $bh]]],
        ];
    }

    protected function styleHeader1(string $t): array
    {
        [,, $h1,,,, , $bh] = $this->tc($t);
        return [
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $h1]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $bh]]],
        ];
    }

    protected function styleHeader2(string $t): array
    {
        [,,, $h2,,,, $bh] = $this->tc($t);
        return [
            'font'      => ['bold' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $h2]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $bh]]],
        ];
    }

    protected function styleFooter(string $t): array
    {
        [,,,, $foot,,, $bh] = $this->tc($t);
        return [
            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $foot]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $bh]]],
        ];
    }

    protected function styleData(int $no, string $t): array
    {
        [,,,,, $stripe,, , $bd] = $this->tc($t);
        $bg = ($no % 2 === 1) ? $stripe : 'FFFFFF';
        return [
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $bd]]],
            'font'    => ['name' => 'Arial', 'size' => 9],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // WORKSHEET HELPERS — Penting: merge SEBELUM set value & style
    // ─────────────────────────────────────────────────────────────────────────

    protected function writeTitle(Worksheet $ws, int $colCount, string $text, string $theme): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        // Merge dulu, set value, baru style
        $ws->mergeCells("A1:{$lastCol}1");
        $ws->setCellValue('A1', $text);
        $ws->getStyle("A1:{$lastCol}1")->applyFromArray($this->styleRowTitle($theme));
        $ws->getRowDimension(1)->setRowHeight(28);
    }

    protected function writeSub(Worksheet $ws, int $colCount, string $text, string $theme): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $ws->mergeCells("A2:{$lastCol}2");
        $ws->setCellValue('A2', $text);
        $ws->getStyle("A2:{$lastCol}2")->applyFromArray($this->styleRowSub($theme));
        $ws->getRowDimension(2)->setRowHeight(15);
    }

    protected function writeKet(Worksheet $ws, int $colCount, int $row, string $text): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $ws->mergeCells("A{$row}:{$lastCol}{$row}");
        $ws->setCellValue("A{$row}", $text);
        $ws->getStyle("A{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UTILITIES
    // ─────────────────────────────────────────────────────────────────────────
    protected function col(int $idx): string
    {
        return Coordinate::stringFromColumnIndex($idx);
    }

    protected function bulanLabel(): array
    {
        return [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
    }

    protected function bulanNama(): array
    {
        return [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    }

    protected function streamXlsx(Spreadsheet $spreadsheet, string $filename): void
    {
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        if (ob_get_level()) ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}