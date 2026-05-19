<?php

namespace App\Exports;

use App\Models\RekapHarianTanam;
use App\Models\Kecamatan;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class RekapHarianTanamExport
{
    protected int    $tahun;
    protected int    $bulan;
    protected string $bulanName;

    // Column index constants
    private const IDX_A  = 1;
    private const IDX_B  = 2;
    private const IDX_C  = 3;
    private const IDX_D  = 4;
    private const IDX_E  = 5;
    private const IDX_F  = 6;
    private const IDX_AJ = 36;
    private const IDX_AK = 37;
    private const IDX_AL = 38;
    private const IDX_AM = 39;
    private const IDX_AN = 40;
    private const IDX_AO = 41;
    private const IDX_AP = 42;

    const H1_BG         = 'BBF7D0';
    const H1_TEXT       = '1F2937';
    const H2_BG         = 'DCFCE7';
    const H2_TEXT       = '374151';
    const STRIPE_ODD    = 'F9FAFB';
    const STRIPE_EVEN   = 'FFFFFF';
    const JUMLAH_BG     = 'E5E7EB';
    const JUMLAH_TEXT   = '1F2937';
    const TOTAL_BG      = '16A34A';
    const TOTAL_TEXT    = 'FFFFFF';
    const BORDER_HEADER = '86EFAC';
    const BORDER_DATA   = 'D1D5DB';
    const BORDER_JUMLAH = '9CA3AF';
    const TITLE_BG      = 'DCFCE7';
    const TITLE_TEXT    = '14532D';
    const SUB_BG        = 'F0FDF4';
    const SUB_TEXT      = '166534';
    const FONT          = 'Arial';

    private static array $BULAN_NAMES = [
        1  => 'JANUARI',
        2  => 'FEBRUARI',
        3  => 'MARET',
        4  => 'APRIL',
        5  => 'MEI',
        6  => 'JUNI',
        7  => 'JULI',
        8  => 'AGUSTUS',
        9  => 'SEPTEMBER',
        10 => 'OKTOBER',
        11 => 'NOVEMBER',
        12 => 'DESEMBER',
    ];

    public function __construct($tahun, $bulan)
    {
        $this->tahun     = (int) $tahun;
        $this->bulan     = (int) $bulan;
        $this->bulanName = self::$BULAN_NAMES[$this->bulan];
    }

    // ── helpers ───────────────────────────────────────────────────────────────

    private function c(int $idx): string
    {
        return Coordinate::stringFromColumnIndex($idx);
    }

    private function colA(): string  { return $this->c(self::IDX_A);  }
    private function colB(): string  { return $this->c(self::IDX_B);  }
    private function colC(): string  { return $this->c(self::IDX_C);  }
    private function colD(): string  { return $this->c(self::IDX_D);  }
    private function colE(): string  { return $this->c(self::IDX_E);  }
    private function colF(): string  { return $this->c(self::IDX_F);  }
    private function colAJ(): string { return $this->c(self::IDX_AJ); }
    private function colAK(): string { return $this->c(self::IDX_AK); }
    private function colAL(): string { return $this->c(self::IDX_AL); }
    private function colAM(): string { return $this->c(self::IDX_AM); }
    private function colAN(): string { return $this->c(self::IDX_AN); }
    private function colAO(): string { return $this->c(self::IDX_AO); }
    private function colAP(): string { return $this->c(self::IDX_AP); }

    /**
     * Returns all column letters in a consistent order.
     *
     * @return string[] [A, B, C, D, E, F, AJ, AK, AL, AM, AN, AO, AP]
     */
    private function cols(): array
    {
        return [
            $this->colA(), $this->colB(), $this->colC(), $this->colD(),
            $this->colE(), $this->colF(), $this->colAJ(), $this->colAK(),
            $this->colAL(), $this->colAM(), $this->colAN(), $this->colAO(),
            $this->colAP(),
        ];
    }

    private function applyH1(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::H1_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::H1_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
    }

    private function applyH2(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => self::FONT, 'color' => ['rgb' => self::H2_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::H2_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
    }

    private function applyTotal(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::TOTAL_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TOTAL_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::TOTAL_BG]]],
        ]);
    }

    /**
     * Format angka: bulat → "0", desimal → "0.00"
     * Sel kosong / null diisi 0 numeric.
     */
private function applyNumberFormat(Worksheet $sheet, string $range): void
    {
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

    private function buildRows(): Collection
    {
        $allKecamatan = Kecamatan::with('kabupaten')
            ->orderBy('kabupaten_id')
            ->orderBy('nama_kecamatan')
            ->get();

        $rekapsData = RekapHarianTanam::with(['kabupaten', 'kecamatan'])
            ->whereYear('tanggal', $this->tahun)
            ->whereMonth('tanggal', $this->bulan)
            ->get()
            ->keyBy('kecamatan_id');

        return $allKecamatan->map(function (Kecamatan $kec) use ($rekapsData) {
            /** @var RekapHarianTanam|null $rekap */
            $rekap = $rekapsData->get($kec->id);

            if ($rekap) {
                return $rekap;
            }

            // Buat objek kosong agar kolom tgl_1..31, oplah, gogo, csr = 0
            $empty = new RekapHarianTanam([
                'kabupaten_id' => $kec->kabupaten_id,
                'kecamatan_id' => $kec->id,
                'target'       => 0,
                'total_tanam'  => 0,
                'oplah'        => 0,
                'gogo'         => 0,
                'csr'          => 0,
                'total_ltt'    => 0,
                'realisasi'    => 0,
            ]);
            for ($d = 1; $d <= 31; $d++) {
                $empty->{"tgl_{$d}"} = 0;
            }
            $empty->setRelation('kabupaten', $kec->kabupaten);
            $empty->setRelation('kecamatan', $kec);

            return $empty;
        });
    }

    // ── sheet building ────────────────────────────────────────────────────────

    private function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName(self::FONT);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("LUAS TANAM {$this->bulanName}");

        $this->setColumnWidths($sheet);
        $this->writeTitleRows($sheet);
        $this->writeHeaderRows($sheet);

        return $spreadsheet;
    }

    private function setColumnWidths(Worksheet $sheet): void
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

    private function writeTitleRows(Worksheet $sheet): void
    {
        $A  = $this->colA();
        $AP = $this->colAP();

        $sheet->setCellValue("{$A}1", "LUAS TANAM PADI — BULAN {$this->bulanName} {$this->tahun}");
        $sheet->mergeCells("{$A}1:{$AP}1");
        $sheet->getStyle("{$A}1:{$AP}1")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'name' => self::FONT, 'color' => ['rgb' => self::TITLE_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue("{$A}2", "Rekapitulasi Luas Tanam Harian per Kecamatan/Kabupaten di Sumatera Selatan");
        $sheet->mergeCells("{$A}2:{$AP}2");
        $sheet->getStyle("{$A}2:{$AP}2")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'name' => self::FONT, 'color' => ['rgb' => self::SUB_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::SUB_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
    }

    private function writeHeaderRows(Worksheet $sheet): void
    {
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $this->cols();

        $sheet->setCellValue("{$A}3", 'No');
        $sheet->setCellValue("{$B}3", 'Kabupaten');
        $sheet->setCellValue("{$C}3", 'No');
        $sheet->setCellValue("{$D}3", 'Kecamatan');
        $sheet->setCellValue("{$E}3", 'Target');
        $sheet->setCellValue("{$F}3", 'TANGGAL');
        $sheet->setCellValue("{$AK}3", 'LTT REGULER');
        $sheet->setCellValue("{$AL}3", 'TOTAL');
        $sheet->setCellValue("{$AO}3", 'TOTAL LTT');
        $sheet->setCellValue("{$AP}3", 'Realisasi - Target');

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

        for ($d = 1; $d <= 31; $d++) {
            $sheet->setCellValue($this->c(self::IDX_F + $d - 1) . '4', $d);
        }
        $sheet->setCellValue("{$AL}4", 'OPLAH');
        $sheet->setCellValue("{$AM}4", 'GOGO');
        $sheet->setCellValue("{$AN}4", 'CSR');

        $this->applyH1($sheet, "{$A}3:{$AP}3");
        $this->applyH2($sheet, "{$A}4:{$AP}4");
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(20);
    }

    private function writeDataRows(Worksheet $sheet, Collection $grouped): array
    {
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $this->cols();

        $row        = 5;
        $noKab      = 1;
        $merges     = [];
        $jumlahRows = [];

        foreach ($grouped as $kabRekaps) {
            $kabStartRow = $row;
            $noKec       = 1;

            $sheet->setCellValue("{$A}{$row}", $noKab);
            $sheet->setCellValue("{$B}{$row}", strtoupper($kabRekaps->first()->kabupaten->nama_kabupaten ?? ''));

            foreach ($kabRekaps as $r) {
                $this->writeKecamatanRow($sheet, $r, $row, $noKec);
                $noKec++;
                $row++;
            }

            $kabEndRow = $row - 1;
            $jRow      = $row;

            $this->writeJumlahRow($sheet, $jRow, $kabStartRow, $kabEndRow);
            $jumlahRows[] = $jRow;
            $merges[]     = ["{$A}{$kabStartRow}:{$A}{$jRow}", "{$B}{$kabStartRow}:{$B}{$jRow}"];

            $row++;
            $noKab++;
        }

        if ($row > 5) {
            $sheet->getStyle("{$A}5:{$AP}".($row-1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_DATA]]],
                'font'    => ['name' => self::FONT, 'size' => 9, 'color' => ['rgb' => '374151']],
            ]);
        }
        if ($row > 5) {
            $sheet->getStyle("{$A}5:{$C}".($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$D}5:{$D}".($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("{$E}5:{$AP}".($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $this->applyNumberFormat($sheet, "{$E}5:{$AP}".($row-1));
        }
        return [$row, $merges, $jumlahRows];
    }

    private function writeKecamatanRow(Worksheet $sheet, RekapHarianTanam $r, int $row, int $noKec): void
    {
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $this->cols();

        $sheet->setCellValue("{$C}{$row}", $noKec);
        $sheet->setCellValue("{$D}{$row}", strtoupper($r->kecamatan->nama_kecamatan ?? ''));
        $valT = (float)($r->target ?? 0);
        $sheet->setCellValue("{$E}{$row}", $valT);
for ($d = 1; $d <= 31; $d++) {
            $cl = $this->c(self::IDX_F + $d - 1);
            $vTgl = (float)($r->{"tgl_{$d}"} ?? 0);
            $sheet->setCellValue("{$cl}{$row}", $vTgl);
}

        // LTT Reguler = SUM tgl_1..tgl_31
        $sheet->setCellValue("{$AK}{$row}", "=SUM({$F}{$row}:{$AJ}{$row})");
        $vOplah = (float)($r->oplah ?? 0);
        $sheet->setCellValue("{$AL}{$row}", $vOplah);
$vGogo = (float)($r->gogo ?? 0);
        $sheet->setCellValue("{$AM}{$row}", $vGogo);
$vCsr = (float)($r->csr ?? 0);
        $sheet->setCellValue("{$AN}{$row}", $vCsr);
// Total LTT = LTT Reguler + OPLAH + GOGO + CSR
        $sheet->setCellValue("{$AO}{$row}", "={$AK}{$row}+{$AL}{$row}+{$AM}{$row}+{$AN}{$row}");
        // Realisasi - Target
        $sheet->setCellValue("{$AP}{$row}", "={$E}{$row}-{$AO}{$row}");

                $sheet->getRowDimension($row)->setRowHeight(15);
    }

    private function writeJumlahRow(Worksheet $sheet, int $jRow, int $kabStartRow, int $kabEndRow): void
    {
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $this->cols();

        $sheet->mergeCells("{$C}{$jRow}:{$D}{$jRow}");
        $sheet->setCellValue("{$C}{$jRow}", 'Jumlah');
        $sheet->setCellValue("{$E}{$jRow}", "=SUM({$E}{$kabStartRow}:{$E}{$kabEndRow})");

        for ($d = 1; $d <= 31; $d++) {
            $cl = $this->c(self::IDX_F + $d - 1);
            $sheet->setCellValue("{$cl}{$jRow}", "=SUM({$cl}{$kabStartRow}:{$cl}{$kabEndRow})");
        }
        foreach ([$AK, $AL, $AM, $AN, $AO, $AP] as $cl) {
            $sheet->setCellValue("{$cl}{$jRow}", "=SUM({$cl}{$kabStartRow}:{$cl}{$kabEndRow})");
        }

        $sheet->getStyle("{$A}{$jRow}:{$AP}{$jRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'name' => self::FONT, 'color' => ['rgb' => self::JUMLAH_TEXT]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::JUMLAH_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER_JUMLAH]]],
        ]);
        $sheet->getStyle("{$C}{$jRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("{$E}{$jRow}:{$AP}{$jRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getRowDimension($jRow)->setRowHeight(17);
    }

    private function applyMerges(Worksheet $sheet, array $merges): void
    {
        foreach ($merges as [$rA, $rB]) {
            $sheet->mergeCells($rA);
            $sheet->getStyle($rA)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['bold' => true, 'name' => self::FONT, 'color' => ['rgb' => '14532D']],
            ]);
            $sheet->mergeCells($rB);
            $sheet->getStyle($rB)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['bold' => true, 'name' => self::FONT, 'color' => ['rgb' => '14532D']],
            ]);
        }
    }

    private function writeTotalRow(Worksheet $sheet, int $row, array $jumlahRows): void
    {
        [$A, $B, $C, $D, $E, $F, $AJ, $AK, $AL, $AM, $AN, $AO, $AP] = $this->cols();

        $sheet->mergeCells("{$A}{$row}:{$D}{$row}");
        $sheet->setCellValue("{$A}{$row}", 'TOTAL SUMATERA SELATAN');

        $buildSum = fn(string $col) => '=' . implode('+', array_map(fn($r) => "{$col}{$r}", $jumlahRows));

        $sheet->setCellValue("{$E}{$row}", $buildSum($E));
        for ($d = 1; $d <= 31; $d++) {
            $cl = $this->c(self::IDX_F + $d - 1);
            $sheet->setCellValue("{$cl}{$row}", $buildSum($cl));
        }
        foreach ([$AK, $AL, $AM, $AN, $AO, $AP] as $cl) {
            $sheet->setCellValue("{$cl}{$row}", $buildSum($cl));
        }

        $this->applyTotal($sheet, "{$A}{$row}:{$AP}{$row}");
                $sheet->getStyle("{$E}{$row}:{$AP}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    // ── main ──────────────────────────────────────────────────────────────────

    public function download(): never
    {
        $rows    = $this->buildRows();
        $grouped = $rows->groupBy('kabupaten_id');

        $spreadsheet = $this->buildSpreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        [$nextRow, $merges, $jumlahRows] = $this->writeDataRows($sheet, $grouped);

        $this->applyMerges($sheet, $merges);

        if (!empty($jumlahRows)) {
            $this->writeTotalRow($sheet, $nextRow, $jumlahRows);
        }

        $sheet->freezePane($this->colF() . '5');

        $filename = "Luas_Tanam_{$this->bulanName}_{$this->tahun}.xlsx";
        $writer   = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
}