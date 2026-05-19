<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\RekapBulananPanen;
use App\Models\RekapHarianPanen;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RekapBulananPanenExport
{
    protected int $tahun;

    private const IDX_A = 1;
    private const IDX_B = 2;
    private const IDX_C = 3;
    private const IDX_D = 4;
    private const IDX_E = 5;
    private const IDX_F = 6;
    private const IDX_AJ = 36;
    private const IDX_AK = 37;
    private const IDX_AL = 38;
    private const IDX_AM = 39;
    private const IDX_AN = 40;
    private const IDX_AO = 41;
    private const IDX_AP = 42;

    const H1_BG = 'BBF7D0';
    const H1_TEXT = '1F2937';
    const H2_BG = 'DCFCE7';
    const H2_TEXT = '374151';
    const JUMLAH_BG = 'E5E7EB';
    const JUMLAH_TEXT = '1F2937';
    const TOTAL_BG = '16A34A';
    const TOTAL_TEXT = 'FFFFFF';
    const GOGO_BG = 'F0FDF4';
    const GOGO_TEXT = '14532D';
    const BORDER_HEADER = '86EFAC';
    const BORDER_DATA = 'D1D5DB';
    const BORDER_JUMLAH = '9CA3AF';
    const TITLE_BG = 'DCFCE7';
    const TITLE_TEXT = '14532D';
    const SUB_BG = 'F0FDF4';
    const SUB_TEXT = '166634';
    const FONT = 'Arial';
    const LAST_COL = 'O';
    /**
     * Format angka:
     *  - Bulat / nol  → tanpa desimal  (#,##0)
     *  - Ada desimal  → tepat 2 digit  (#,##0.00)
     */
    const NUM_FMT_INT = '#,##0;-#,##0;0';
    const NUM_FMT_DEC = '#,##0.00;-#,##0.00;0.00';

    private const BULAN_FIELD = [
        1 => 'januari',
        2 => 'februari',
        3 => 'maret',
        4 => 'april',
        5 => 'mei',
        6 => 'juni',
        7 => 'juli',
        8 => 'agustus',
        9 => 'september',
        10 => 'oktober',
        11 => 'november',
        12 => 'desember',
    ];

    private const BULAN_SINGKAT = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];
    private const BULAN_NAMES = [
        1 => 'JANUARI',
        2 => 'FEBRUARI',
        3 => 'MARET',
        4 => 'APRIL',
        5 => 'MEI',
        6 => 'JUNI',
        7 => 'JULI',
        8 => 'AGUSTUS',
        9 => 'SEPTEMBER',
        10 => 'OKTOBER',
        11 => 'NOVEMBER',
        12 => 'DESEMBER',
    ];
    private const BULAN_COLS = [
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F',
        5 => 'G',
        6 => 'H',
        7 => 'I',
        8 => 'J',
        9 => 'K',
        10 => 'L',
        11 => 'M',
        12 => 'N',
    ];

    // ── Cache ─────────────────────────────────────────────────────────────────
    private ?Collection $kabupatenList = null;
    private ?Collection $bulananCache = null;
    private array $harianCache = [];
    private ?Collection $kecamatanList = null;
    private array $gogoSumCache = [];
    private array $oplahSumCache = [];
    private array $csrSumCache = [];
    private array $colTotals = [];

    // ── Shared styles ─────────────────────────────────────────────────────────
    private array $styleH1;
    private array $styleH2;
    private array $styleTotal;
    private bool $stylesBuilt = false;

    public function __construct($tahun)
    {
        $this->tahun = (int) $tahun;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function c(int $idx): string
    {
        return Coordinate::stringFromColumnIndex($idx);
    }

    private function colA(): string
    {
        return $this->c(self::IDX_A);
    }
    private function colB(): string
    {
        return $this->c(self::IDX_B);
    }
    private function colC(): string
    {
        return $this->c(self::IDX_C);
    }
    private function colD(): string
    {
        return $this->c(self::IDX_D);
    }
    private function colE(): string
    {
        return $this->c(self::IDX_E);
    }
    private function colF(): string
    {
        return $this->c(self::IDX_F);
    }
    private function colAJ(): string
    {
        return $this->c(self::IDX_AJ);
    }
    private function colAK(): string
    {
        return $this->c(self::IDX_AK);
    }
    private function colAL(): string
    {
        return $this->c(self::IDX_AL);
    }
    private function colAM(): string
    {
        return $this->c(self::IDX_AM);
    }
    private function colAN(): string
    {
        return $this->c(self::IDX_AN);
    }
    private function colAO(): string
    {
        return $this->c(self::IDX_AO);
    }
    private function colAP(): string
    {
        return $this->c(self::IDX_AP);
    }

    /**
     * 13 elemen: [0]=A [1]=B [2]=C [3]=D [4]=E [5]=F
     *            [6]=AJ [7]=AK [8]=AL [9]=AM [10]=AN [11]=AO [12]=AP
     */
    private function dailyCols(): array
    {
        return [
            $this->colA(),
            $this->colB(),
            $this->colC(),
            $this->colD(),
            $this->colE(),
            $this->colF(),
            $this->colAJ(),
            $this->colAK(),
            $this->colAL(),
            $this->colAM(),
            $this->colAN(),
            $this->colAO(),
            $this->colAP(),
        ];
    }

    /**
     * Baca nilai numerik dari model secara aman.
     * Eloquent cast decimal mengembalikan string|null — konversi ke float.
     */
    private function num($model, string $field): float
    {
        $v = $model->{$field} ?? null;
        if ($v === null || $v === '')
            return 0.0;
        return (float) (string) $v;
    }

    /** Tulis nilai float ke sel. */
    private function writeNum(Worksheet $sheet, string $cell, float $value, array &$decCells = []): void
    {
        $sheet->setCellValue($cell, $value);
        if (fmod(abs($value), 1.0) >= 1e-9) {
            $decCells[] = $cell;
        }
    }

    /** Terapkan format angka dinamis ke range integer, lalu terapkan desimal spesifik. */
    private function applyNumberFormat(Worksheet $sheet, string $range, array $decCells = []): void
    {
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode(self::NUM_FMT_INT);
        foreach ($decCells as $cell) {
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(self::NUM_FMT_DEC);
        }
    }

    /** Tulis formula ke sel. */
    private function writeNumFormula(Worksheet $sheet, string $cell, string $formula, float $preCalcValue = 0.0): void
    {
        $sheet->setCellValue($cell, $formula);
    }

    private function buildStyles(): void
    {
        if ($this->stylesBuilt)
            return;
        $this->styleH1 = [
            'font' => ['bold' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::H1_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::H1_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ];
        $this->styleH2 = [
            'font' => ['bold' => true, 'size' => 9, 'name' => self::FONT, 'color' => ['rgb' => self::H2_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::H2_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ];
        $this->styleTotal = [
            'font' => ['bold' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::TOTAL_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TOTAL_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::TOTAL_BG]]],
        ];
        $this->stylesBuilt = true;
    }

    private function applyH1(Worksheet $sheet, string $range): void
    {
        $this->buildStyles();
        $sheet->getStyle($range)->applyFromArray($this->styleH1);
    }

    private function applyH2(Worksheet $sheet, string $range): void
    {
        $this->buildStyles();
        $sheet->getStyle($range)->applyFromArray($this->styleH2);
    }

    private function applyTotal(Worksheet $sheet, string $range): void
    {
        $this->buildStyles();
        $sheet->getStyle($range)->applyFromArray($this->styleTotal);
    }

    // ── Data pre-loading ──────────────────────────────────────────────────────

    private function preloadAll(): void
    {
        $this->kabupatenList = Kabupaten::orderBy('id')->get();
        $this->kecamatanList = Kecamatan::with('kabupaten')
            ->orderBy('kabupaten_id')->orderBy('id')->get();

        $this->bulananCache = RekapBulananPanen::where('tahun', $this->tahun)
            ->get()->keyBy('kabupaten_id');

        $allHarian = RekapHarianPanen::with(['kabupaten', 'kecamatan'])
            ->whereYear('tanggal', $this->tahun)->get();

        foreach (array_keys(self::BULAN_SINGKAT) as $bNum) {
            $bulanData = $allHarian->filter(
                fn($r) => (int) date('n', strtotime($r->tanggal)) === $bNum
            );
            $this->harianCache[$bNum] = $bulanData->keyBy('kecamatan_id');
            $this->gogoSumCache[$bNum] = (float) $bulanData->sum('gogo');
            $this->oplahSumCache[$bNum] = (float) $bulanData->sum('oplah');
            $this->csrSumCache[$bNum] = (float) $bulanData->sum('csr');
        }
    }

    private function buildBulananRows(): Collection
    {
        return $this->kabupatenList->map(function (Kabupaten $kab) {
            $row = $this->bulananCache->get($kab->id);
            if (!$row) {
                $row = new RekapBulananPanen(['kabupaten_id' => $kab->id, 'tahun' => $this->tahun]);
            }
            $row->setRelation('kabupaten', $kab);
            return $row;
        });
    }

    private function buildHarianRows(int $bulanNum): Collection
    {
        $harianByKec = $this->harianCache[$bulanNum];
        return $this->kecamatanList->map(function (Kecamatan $kec) use ($harianByKec) {
            $rekap = $harianByKec->get($kec->id);
            if ($rekap) {
                if (!$rekap->relationLoaded('kabupaten') || !$rekap->kabupaten)
                    $rekap->setRelation('kabupaten', $kec->kabupaten);
                if (!$rekap->relationLoaded('kecamatan') || !$rekap->kecamatan)
                    $rekap->setRelation('kecamatan', $kec);
                return $rekap;
            }
            $empty = new RekapHarianPanen(['kabupaten_id' => $kec->kabupaten_id, 'kecamatan_id' => $kec->id]);
            $empty->setRelation('kabupaten', $kec->kabupaten);
            $empty->setRelation('kecamatan', $kec);
            return $empty;
        });
    }

    // ── Sheet 1: Rekap Bulanan ────────────────────────────────────────────────

    private function buildRekapBulananSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet(0);
        $sheet->setTitle('REKAP BULANAN');
        $spreadsheet->getDefaultStyle()->getFont()->setName(self::FONT);

        $this->writeRekapTitle($sheet);
        $this->writeRekapHeader($sheet);
        $dataEnd = $this->writeRekapDataRows($sheet, $this->buildBulananRows());
        $jRow = $this->writeRekapJumlahRow($sheet, $dataEnd);
        [$gogoRow, $oplahRow, $csrRow] = $this->writeRekapGogoRows($sheet, $jRow);
        $this->writeRekapTotalRow($sheet, $jRow, $gogoRow, $oplahRow, $csrRow);
        $this->writeRekapKeterangan($sheet);
        $this->setRekapColumnWidths($sheet);
        $sheet->freezePane('C5');
    }

    private function writeRekapTitle(Worksheet $sheet): void
    {
        $lc = self::LAST_COL;
        $sheet->setCellValue('A1', "REKAP BULANAN LUAS TAMBAH PANEN {$this->tahun}");
        $sheet->mergeCells("A1:{$lc}1");
        $sheet->getStyle("A1:{$lc}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'name' => self::FONT, 'color' => ['rgb' => self::TITLE_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Sanding Luas Panen Padi per Kabupaten/Kota di Sumatera Selatan');
        $sheet->mergeCells("A2:{$lc}2");
        $sheet->getStyle("A2:{$lc}2")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::SUB_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::SUB_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
    }

    private function writeRekapHeader(Worksheet $sheet): void
    {
        $lc = self::LAST_COL;
        $sheet->setCellValue('A3', 'NO.');
        $sheet->setCellValue('B3', 'KABUPATEN / KOTA');
        $sheet->setCellValue('C3', "LUAS PANEN (Ha) TAHUN {$this->tahun}");
        $sheet->setCellValue('O3', 'TOTAL');
        $sheet->mergeCells('C3:N3');
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('O3:O4');
        foreach (self::BULAN_COLS as $bNum => $col) {
            $sheet->setCellValue("{$col}4", self::BULAN_SINGKAT[$bNum]);
        }
        $this->applyH1($sheet, "A3:{$lc}3");
        $this->applyH2($sheet, "A4:{$lc}4");
        $sheet->getRowDimension(3)->setRowHeight(26);
        $sheet->getRowDimension(4)->setRowHeight(18);
    }

    private function writeRekapDataRows(Worksheet $sheet, Collection $rekaps): int
    {
        $lc = self::LAST_COL;
        $row = 5;
        $no = 1;
        $decCells = [];
        $this->colTotals = array_fill(1, 12, 0.0);

        foreach ($rekaps as $rekap) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", strtoupper($rekap->kabupaten->nama_kabupaten ?? ''));

            $rowTotal = 0.0;
            foreach (self::BULAN_COLS as $bNum => $col) {
                $val = $this->num($rekap, self::BULAN_FIELD[$bNum]);
                $rowTotal += $val;
                $this->colTotals[$bNum] += $val;
                $this->writeNum($sheet, "{$col}{$row}", $val, $decCells);
            }
            $this->writeNum($sheet, "O{$row}", $rowTotal, $decCells);
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
            $no++;
        }

        $dataEnd = $row - 1;
        if ($dataEnd >= 5) {
            $sheet->getStyle("A5:{$lc}{$dataEnd}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_DATA]]],
                'font' => ['name' => self::FONT, 'size' => 9, 'color' => ['rgb' => '374151']],
            ]);
            $sheet->getStyle("A5:A{$dataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B5:B{$dataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C5:{$lc}{$dataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("O5:O{$dataEnd}")->getFont()->setBold(true);
            $this->applyNumberFormat($sheet, "C5:{$lc}{$dataEnd}", $decCells);
        }
        return $dataEnd;
    }

    private function writeRekapJumlahRow(Worksheet $sheet, int $dataEnd): int
    {
        $lc = self::LAST_COL;
        $jRow = $dataEnd + 1;
        $sheet->mergeCells("A{$jRow}:B{$jRow}");
        $sheet->setCellValue("A{$jRow}", 'J U M L A H');

        $decCells = [];
        $totalSum = 0.0;
        foreach (self::BULAN_COLS as $bNum => $col) {
            $colSum = $this->colTotals[$bNum] ?? 0.0;
            $totalSum += $colSum;
            $this->writeNumFormula($sheet, "{$col}{$jRow}", "=SUM({$col}5:{$col}{$dataEnd})");
            if (fmod(abs($colSum), 1.0) >= 1e-9) $decCells[] = "{$col}{$jRow}";
        }
        $this->writeNumFormula($sheet, "O{$jRow}", "=SUM(O5:O{$dataEnd})");
        if (fmod(abs($totalSum), 1.0) >= 1e-9) $decCells[] = "O{$jRow}";

        $this->applyH1($sheet, "A{$jRow}:{$lc}{$jRow}");
        $sheet->getStyle("C{$jRow}:{$lc}{$jRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->applyNumberFormat($sheet, "C{$jRow}:{$lc}{$jRow}", $decCells);
        $sheet->getRowDimension($jRow)->setRowHeight(22);
        return $jRow;
    }

    private function writeRekapGogoRows(Worksheet $sheet, int $jRow): array
    {
        $lc = self::LAST_COL;
        $gogoRow = $jRow + 1;
        $oplahRow = $jRow + 2;
        $csrRow = $jRow + 3;

        $sheet->setCellValue("B{$gogoRow}", 'Padi Gogo');
        $sheet->setCellValue("B{$oplahRow}", 'Padi OPLAH');
        $sheet->setCellValue("B{$csrRow}", 'Padi CSR');

        $decCells = [];
        foreach (self::BULAN_COLS as $bNum => $col) {
            $gSum = $this->gogoSumCache[$bNum] ?? 0.0;
            $oSum = $this->oplahSumCache[$bNum] ?? 0.0;
            $cSum = $this->csrSumCache[$bNum] ?? 0.0;
            $this->writeNum($sheet, "{$col}{$gogoRow}", $gSum, $decCells);
            $this->writeNum($sheet, "{$col}{$oplahRow}", $oSum, $decCells);
            $this->writeNum($sheet, "{$col}{$csrRow}", $cSum, $decCells);
        }

        $gtg = array_sum($this->gogoSumCache);
        $gto = array_sum($this->oplahSumCache);
        $gtc = array_sum($this->csrSumCache);
        $this->writeNumFormula($sheet, "O{$gogoRow}", "=SUM(C{$gogoRow}:N{$gogoRow})");
        if (fmod(abs($gtg), 1.0) >= 1e-9) $decCells[] = "O{$gogoRow}";
        $this->writeNumFormula($sheet, "O{$oplahRow}", "=SUM(C{$oplahRow}:N{$oplahRow})");
        if (fmod(abs($gto), 1.0) >= 1e-9) $decCells[] = "O{$oplahRow}";
        $this->writeNumFormula($sheet, "O{$csrRow}", "=SUM(C{$csrRow}:N{$csrRow})");
        if (fmod(abs($gtc), 1.0) >= 1e-9) $decCells[] = "O{$csrRow}";

        $sheet->getStyle("B{$gogoRow}:{$lc}{$csrRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::GOGO_BG]],
            'font' => ['name' => self::FONT, 'size' => 9, 'italic' => true, 'color' => ['rgb' => self::GOGO_TEXT]],
        ]);
        $sheet->getStyle("C{$gogoRow}:{$lc}{$csrRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->applyNumberFormat($sheet, "C{$gogoRow}:{$lc}{$csrRow}", $decCells);
        return [$gogoRow, $oplahRow, $csrRow];
    }

    private function writeRekapTotalRow(Worksheet $sheet, int $jRow, int $gogoRow, int $oplahRow, int $csrRow): void
    {
        $lc = self::LAST_COL;
        $totalRow = $csrRow + 1;
        $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');

        $decCells = [];
        $totalSum = 0.0;
        foreach (self::BULAN_COLS as $bNum => $col) {
            $colTotal = ($this->colTotals[$bNum] ?? 0.0) + ($this->gogoSumCache[$bNum] ?? 0.0) + ($this->oplahSumCache[$bNum] ?? 0.0) + ($this->csrSumCache[$bNum] ?? 0.0);
            $totalSum += $colTotal;
            $this->writeNumFormula($sheet, "{$col}{$totalRow}", "={$col}{$jRow}+{$col}{$gogoRow}+{$col}{$oplahRow}+{$col}{$csrRow}");
            if (fmod(abs($colTotal), 1.0) >= 1e-9) $decCells[] = "{$col}{$totalRow}";
        }
        $this->writeNumFormula($sheet, "O{$totalRow}", "=SUM(O{$jRow}:O{$csrRow})");
        if (fmod(abs($totalSum), 1.0) >= 1e-9) $decCells[] = "O{$totalRow}";

        $this->applyTotal($sheet, "A{$totalRow}:{$lc}{$totalRow}");
        $sheet->getStyle("C{$totalRow}:{$lc}{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->applyNumberFormat($sheet, "C{$totalRow}:{$lc}{$totalRow}", $decCells);
        $sheet->getRowDimension($totalRow)->setRowHeight(22);
    }

    private function writeRekapKeterangan(Worksheet $sheet): void
    {
        $kRow = $sheet->getHighestRow() + 1;
        $lc = self::LAST_COL;
        $sheet->mergeCells("A{$kRow}:{$lc}{$kRow}");
        $sheet->setCellValue("A{$kRow}", '* Satuan: Hektar (Ha)  |  Tahun: ' . $this->tahun);
        $sheet->getStyle("A{$kRow}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '9CA3AF'], 'name' => self::FONT],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension($kRow)->setRowHeight(14);
    }

    private function setRekapColumnWidths(Worksheet $sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(28);
        foreach (self::BULAN_COLS as $col) {
            $sheet->getColumnDimension($col)->setWidth(11);
        }
        $sheet->getColumnDimension('O')->setWidth(12);
    }

    // ── Sheet 2-13: Harian per Bulan ─────────────────────────────────────────

    private function buildLuasPanenSheet(Spreadsheet $spreadsheet, int $bulanNum, string $bulanName): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('LUAS PANEN ' . substr($bulanName, 0, 3));
        $this->setDailyColumnWidths($sheet);
        $this->writeDailyTitle($sheet, $bulanName);
        $this->writeDailyHeader($sheet, 'LTP REGULER', 'TOTAL LTP');
        $grouped = $this->buildHarianRows($bulanNum)->groupBy('kabupaten_id');
        [$nextRow, $merges, $jumlahRows] = $this->writeDailyDataRows($sheet, $grouped);
        $this->applyDailyMerges($sheet, $merges);
        $this->writeDailyTotalRow($sheet, $nextRow, $jumlahRows);
        $sheet->freezePane($this->colF() . '5');
    }

    private function setDailyColumnWidths(Worksheet $sheet): void
    {
        $sheet->getColumnDimension($this->colA())->setWidth(5);
        $sheet->getColumnDimension($this->colB())->setWidth(22);
        $sheet->getColumnDimension($this->colC())->setWidth(5);
        $sheet->getColumnDimension($this->colD())->setWidth(26);
        $sheet->getColumnDimension($this->colE())->setWidth(10);
        for ($i = self::IDX_F; $i <= self::IDX_AJ; $i++) {
            $sheet->getColumnDimension($this->c($i))->setWidth(6.5);
        }
        $sheet->getColumnDimension($this->colAK())->setWidth(12);
        $sheet->getColumnDimension($this->colAL())->setWidth(10);
        $sheet->getColumnDimension($this->colAM())->setWidth(10);
        $sheet->getColumnDimension($this->colAN())->setWidth(10);
        $sheet->getColumnDimension($this->colAO())->setWidth(12);
        $sheet->getColumnDimension($this->colAP())->setWidth(14);
    }

    private function writeDailyTitle(Worksheet $sheet, string $bulanName): void
    {
        $cols = $this->dailyCols();
        $A = $cols[0];
        $AP = $cols[12];

        $sheet->setCellValue("{$A}1", "LUAS PANEN PADI — BULAN {$bulanName} {$this->tahun}");
        $sheet->mergeCells("{$A}1:{$AP}1");
        $sheet->getStyle("{$A}1:{$AP}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'name' => self::FONT, 'color' => ['rgb' => self::TITLE_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue("{$A}2", 'Rekapitulasi Luas Panen Harian per Kecamatan/Kabupaten di Sumatera Selatan');
        $sheet->mergeCells("{$A}2:{$AP}2");
        $sheet->getStyle("{$A}2:{$AP}2")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::SUB_TEXT]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::SUB_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
    }

    private function writeDailyHeader(Worksheet $sheet, string $lttLabel, string $totalLabel): void
    {
        $cols = $this->dailyCols();
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $cols;

        $sheet->setCellValue("{$A}3", 'No');
        $sheet->setCellValue("{$B}3", 'Kabupaten');
        $sheet->setCellValue("{$C}3", 'No');
        $sheet->setCellValue("{$D}3", 'Kecamatan');
        $sheet->setCellValue("{$E}3", 'Target');
        $sheet->setCellValue("{$F}3", 'TANGGAL');
        $sheet->setCellValue("{$AK}3", $lttLabel);
        $sheet->setCellValue("{$AL}3", 'TOTAL');
        $sheet->setCellValue("{$AO}3", $totalLabel);
        $sheet->setCellValue("{$AP}3", 'Realisasi - Target');

        for ($d = 1; $d <= 31; $d++) {
            $sheet->setCellValue($this->c(self::IDX_F + $d - 1) . '4', $d);
        }
        $sheet->setCellValue("{$AL}4", 'OPLAH');
        $sheet->setCellValue("{$AM}4", 'GOGO');
        $sheet->setCellValue("{$AN}4", 'CSR');

        $sheet->mergeCells("{$A}3:{$A}4");
        $sheet->mergeCells("{$B}3:{$B}4");
        $sheet->mergeCells("{$C}3:{$C}4");
        $sheet->mergeCells("{$D}3:{$D}4");
        $sheet->mergeCells("{$E}3:{$E}4");
        $sheet->mergeCells("{$F}3:{$AJ}3");
        $sheet->mergeCells("{$AK}3:{$AK}4");
        $sheet->mergeCells("{$AL}3:{$AN}3");
        $sheet->mergeCells("{$AO}3:{$AO}4");
        $sheet->mergeCells("{$AP}3:{$AP}4");

        $this->applyH1($sheet, "{$A}3:{$AP}3");
        $this->applyH2($sheet, "{$A}4:{$AP}4");
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(20);
    }

    private function writeDailyDataRows(Worksheet $sheet, Collection $grouped): array
    {
        $row = 5;
        $noKab = 1;
        $merges = [];
        $jumlahRows = [];
        $dataMatrix = []; 
        $decCells = [];

        foreach ($grouped as $kabRekaps) {
            $kabStartRow = $row;
            $noKec = 1;
            $namaKab = strtoupper($kabRekaps->first()->kabupaten->nama_kabupaten ?? '');
            $colSums = array_fill(4, 38, 0.0);
            
            foreach ($kabRekaps as $r) {
                $rowData = array_fill(0, 42, null);
                if ($row === $kabStartRow) {
                    $rowData[0] = $noKab;
                    $rowData[1] = $namaKab;
                }
                
                $rowData[2] = $noKec;
                $rowData[3] = strtoupper($r->kecamatan->nama_kecamatan ?? '');
                $target = $this->num($r, 'target');
                $rowData[4] = $target;
                if (fmod(abs($target), 1.0) >= 1e-9) $decCells[] = "E{$row}";
                $colSums[4] += $target;

                $tglSum = 0.0;
                for ($d = 1; $d <= 31; $d++) {
                    $val = $this->num($r, "tgl_{$d}");
                    $tglSum += $val;
                    $rowData[4 + $d] = $val;
                    if (fmod(abs($val), 1.0) >= 1e-9) {
                        $cl = Coordinate::stringFromColumnIndex(6 + $d - 1);
                        $decCells[] = "{$cl}{$row}";
                    }
                    $colSums[4 + $d] += $val;
                }
                
                $rowData[36] = "=SUM(F{$row}:AJ{$row})"; 
                if (fmod(abs($tglSum), 1.0) >= 1e-9) $decCells[] = "AK{$row}";
                $colSums[36] += $tglSum;
                
                $oplah = $this->num($r, 'oplah');
                $gogo = $this->num($r, 'gogo');
                $csr = $this->num($r, 'csr');
                $rowData[37] = $oplah; 
                $rowData[38] = $gogo; 
                $rowData[39] = $csr; 
                if (fmod(abs($oplah), 1.0) >= 1e-9) $decCells[] = "AL{$row}";
                if (fmod(abs($gogo), 1.0) >= 1e-9) $decCells[] = "AM{$row}";
                if (fmod(abs($csr), 1.0) >= 1e-9) $decCells[] = "AN{$row}";
                $colSums[37] += $oplah;
                $colSums[38] += $gogo;
                $colSums[39] += $csr;
                
                $totalLtp = $tglSum + $oplah + $gogo + $csr;
                $rowData[40] = "=AK{$row}+AL{$row}+AM{$row}+AN{$row}";
                if (fmod(abs($totalLtp), 1.0) >= 1e-9) $decCells[] = "AO{$row}";
                $colSums[40] += $totalLtp;
                
                $selisih = $totalLtp - $target;
                $rowData[41] = "=AO{$row}-E{$row}"; 
                if (fmod(abs($selisih), 1.0) >= 1e-9) $decCells[] = "AP{$row}";
                $colSums[41] += $selisih;
                
                $dataMatrix[] = $rowData;
                $sheet->getRowDimension($row)->setRowHeight(15);
                $noKec++;
                $row++;
            }
            
            $kabEndRow = $row - 1;
            $jRow = $row;
            $jumlahRows[] = $jRow;
            $merges[] = ["A{$kabStartRow}:A{$jRow}", "B{$kabStartRow}:B{$jRow}"];
            
            $jRowData = array_fill(0, 42, null);
            $jRowData[2] = 'Jumlah';
            $jRowData[4] = "=SUM(E{$kabStartRow}:E{$kabEndRow})";
            if (fmod(abs($colSums[4]), 1.0) >= 1e-9) $decCells[] = "E{$jRow}";
            
            for ($d = 1; $d <= 31; $d++) {
                $cl = Coordinate::stringFromColumnIndex(6 + $d - 1);
                $jRowData[4 + $d] = "=SUM({$cl}{$kabStartRow}:{$cl}{$kabEndRow})";
                if (fmod(abs($colSums[4 + $d]), 1.0) >= 1e-9) $decCells[] = "{$cl}{$jRow}";
            }
            
            $jRowData[36] = "=SUM(AK{$kabStartRow}:AK{$kabEndRow})";
            if (fmod(abs($colSums[36]), 1.0) >= 1e-9) $decCells[] = "AK{$jRow}";
            $jRowData[37] = "=SUM(AL{$kabStartRow}:AL{$kabEndRow})";
            if (fmod(abs($colSums[37]), 1.0) >= 1e-9) $decCells[] = "AL{$jRow}";
            $jRowData[38] = "=SUM(AM{$kabStartRow}:AM{$kabEndRow})";
            if (fmod(abs($colSums[38]), 1.0) >= 1e-9) $decCells[] = "AM{$jRow}";
            $jRowData[39] = "=SUM(AN{$kabStartRow}:AN{$kabEndRow})";
            if (fmod(abs($colSums[39]), 1.0) >= 1e-9) $decCells[] = "AN{$jRow}";
            $jRowData[40] = "=SUM(AO{$kabStartRow}:AO{$kabEndRow})";
            if (fmod(abs($colSums[40]), 1.0) >= 1e-9) $decCells[] = "AO{$jRow}";
            $jRowData[41] = "=SUM(AP{$kabStartRow}:AP{$kabEndRow})";
            if (fmod(abs($colSums[41]), 1.0) >= 1e-9) $decCells[] = "AP{$jRow}";
            
            $dataMatrix[] = $jRowData;
            
            $row++;
            $noKab++;
        }

        if (!empty($dataMatrix)) {
            $sheet->fromArray($dataMatrix, null, 'A5', true);
            $end = $row - 1;
            
            $sheet->getStyle("A5:AP{$end}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_DATA]]],
                'font' => ['name' => self::FONT, 'size' => 9, 'color' => ['rgb' => '374151']],
            ]);
            $sheet->getStyle("E5:AP{$end}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $this->applyNumberFormat($sheet, "E5:AP{$end}", $decCells);
            
            foreach ($jumlahRows as $jR) {
                $sheet->getStyle("A{$jR}:AP{$jR}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'name' => self::FONT, 'color' => ['rgb' => self::JUMLAH_TEXT]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::JUMLAH_BG]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_JUMLAH]]],
                ]);
                $sheet->getStyle("C{$jR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$jR}:AP{$jR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->mergeCells("C{$jR}:D{$jR}");
                $sheet->getRowDimension($jR)->setRowHeight(17);
            }
        }
        
        return [$row, $merges, $jumlahRows];
    }

    private function applyDailyMerges(Worksheet $sheet, array $merges): void
    {
        $style = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'font' => ['bold' => true, 'name' => self::FONT, 'color' => ['rgb' => '14532D']],
        ];
        foreach ($merges as [$rA, $rB]) {
            $sheet->mergeCells($rA);
            $sheet->getStyle($rA)->applyFromArray($style);
            $sheet->mergeCells($rB);
            $sheet->getStyle($rB)->applyFromArray($style);
        }
    }

    private function writeDailyTotalRow(Worksheet $sheet, int $row, array $jumlahRows): void
    {
        $cols = $this->dailyCols();
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $cols;

        $sheet->mergeCells("{$A}{$row}:{$D}{$row}");
        $sheet->setCellValue("{$A}{$row}", 'TOTAL SUMATERA SELATAN');

        $decCells = [];
        $colSums = array_fill(4, 38, 0.0);

        foreach ($jumlahRows as $jRow) {
            $colSums[4] += (float) $sheet->getCell("E{$jRow}")->getCalculatedValue();
            for ($d = 1; $d <= 31; $d++) {
                $cl = Coordinate::stringFromColumnIndex(6 + $d - 1);
                $colSums[4 + $d] += (float) $sheet->getCell("{$cl}{$jRow}")->getCalculatedValue();
            }
            $colSums[36] += (float) $sheet->getCell("AK{$jRow}")->getCalculatedValue();
            $colSums[37] += (float) $sheet->getCell("AL{$jRow}")->getCalculatedValue();
            $colSums[38] += (float) $sheet->getCell("AM{$jRow}")->getCalculatedValue();
            $colSums[39] += (float) $sheet->getCell("AN{$jRow}")->getCalculatedValue();
            $colSums[40] += (float) $sheet->getCell("AO{$jRow}")->getCalculatedValue();
            $colSums[41] += (float) $sheet->getCell("AP{$jRow}")->getCalculatedValue();
        }

        $calcSumOrFormat = function (string $col, int $idx) use ($sheet, $jumlahRows, $row, $colSums, &$decCells) {
            $formulaArgs = array_map(fn($r) => "{$col}{$r}", $jumlahRows);
            $this->writeNumFormula($sheet, "{$col}{$row}", "=SUM(" . implode(',', $formulaArgs) . ")");
            if (fmod(abs($colSums[$idx]), 1.0) >= 1e-9) $decCells[] = "{$col}{$row}";
        };

        $calcSumOrFormat('E', 4);
        for ($d = 1; $d <= 31; $d++) {
            $calcSumOrFormat(Coordinate::stringFromColumnIndex(6 + $d - 1), 4 + $d);
        }
        $calcSumOrFormat('AK', 36);
        $calcSumOrFormat('AL', 37);
        $calcSumOrFormat('AM', 38);
        $calcSumOrFormat('AN', 39);
        $calcSumOrFormat('AO', 40);
        $calcSumOrFormat('AP', 41);

        $this->applyTotal($sheet, "{$A}{$row}:{$AP}{$row}");
        $sheet->getStyle("{$E}{$row}:{$AP}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->applyNumberFormat($sheet, "{$E}{$row}:{$AP}{$row}", $decCells);
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    // ── Main ──────────────────────────────────────────────────────────────────

    public function download(): never
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $this->preloadAll();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $this->buildRekapBulananSheet($spreadsheet);

        foreach (self::BULAN_NAMES as $num => $name) {
            $this->buildLuasPanenSheet($spreadsheet, $num, $name);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rekappanen-' . $this->tahun . '.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}