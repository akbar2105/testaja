<?php

namespace App\Exports;

use App\Models\RekapTahunanTanam;
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
 * RekapTahunanTanamExport
 *
 * FIX:
 *  - Header "Total LTT Januari – Desember" → merge C3 s.d. kolom tahun terakhir ($lastCol)
 *  - Footer "SUMATERA SELATAN" → merge A:B (bukan A saja)
 *  - Label footer muncul di cell A (terisi data array)
 */
class RekapTahunanTanamExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $tahunList;

    // ── Warna identik dengan RekapBulananTanamExport ──────────────────────────
    const H1_BG         = 'BBF7D0'; // green-200
    const H1_TEXT       = '1F2937'; // gray-800
    const H2_BG         = 'DCFCE7'; // green-100
    const H2_TEXT       = '374151'; // gray-700
    const STRIPE_ODD    = 'F9FAFB'; // gray-50
    const STRIPE_EVEN   = 'FFFFFF'; // putih
    const TOTAL_BG      = '16A34A'; // green-600
    const TOTAL_TEXT    = 'FFFFFF'; // putih
    const BORDER_HEADER = '86EFAC'; // green-300
    const BORDER_DATA   = 'D1D5DB'; // gray-300
    const TITLE_BG      = 'DCFCE7'; // green-100
    const TITLE_TEXT    = '14532D'; // green-900
    const SUB_BG        = 'F0FDF4'; // green-50
    const SUB_TEXT      = '166534'; // green-800
    const FMT           = '#,##0.00';
    const FONT          = 'Arial';

    public function __construct(array $tahunList)
    {
        $this->tahunList = $tahunList;
    }

    public function title(): string
    {
        return 'Rekap Tahunan Tanam';
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

    /* ── DATA ─────────────────────────────────────────────────────────────── */
    public function array(): array
    {
        $kabupatens    = Kabupaten::orderBy('id')->get();
        $data          = RekapTahunanTanam::whereIn('tahun', $this->tahunList)->get()->groupBy('kabupaten_id');
        $totalPerTahun = RekapTahunanTanam::whereIn('tahun', $this->tahunList)
            ->select('tahun', DB::raw('SUM(total) as grand_total'))
            ->groupBy('tahun')->pluck('grand_total', 'tahun');

        $nT   = count($this->tahunList);
        $rows = [];

        // Baris 1 — Judul
        $rows[] = array_merge(
            ['REKAP TAHUNAN LUAS TANAM PADI'],
            array_fill(0, $nT + 1, '')
        );

        // Baris 2 — Sub judul
        $rows[] = array_merge(
            ['Sanding Total LTT Padi Januari–Desember per Kabupaten/Kota di Sumatera Selatan'],
            array_fill(0, $nT + 1, '')
        );

        // Baris 3 — Header baris-1
        // Kolom A: No | Kolom B: Kabupaten/Kota | Kolom C–lastCol: "Total LTT Januari – Desember" (merge di styles)
        $h1 = ['No', 'Kabupaten/Kota'];
        for ($i = 0; $i < $nT; $i++) $h1[] = 'Total LTT Januari – Desember'; // nanti di-merge
        $h1[] = ''; // Untuk TOTAL
        $rows[] = $h1;

        // Baris 4 — Label tahun (sub-header)
        $h2 = ['', ''];
        foreach ($this->tahunList as $t) $h2[] = (string) $t;
        $h2[] = 'TOTAL';
        $rows[] = $h2;

        // Data kabupaten
        $no = 1;
        foreach ($kabupatens as $kab) {
            $row = [$no++, strtoupper($kab->nama_kabupaten)];
            $rowTotal = 0;
            foreach ($this->tahunList as $t) {
                $total = $data->get($kab->id)?->firstWhere('tahun', $t)?->total ?? 0;
                $row[] = $this->formatData($total);
                $rowTotal += $total;
            }
            $row[] = $this->formatData($rowTotal);
            $rows[] = $row;
        }

        // Baris footer — SUMATERA SELATAN
        // Kolom A = label (akan merge A:B di styles)
        $rowTotal = ['Total', ''];
        $grandTotalInfo = 0;
        foreach ($this->tahunList as $t) {
            $v = (float) $totalPerTahun->get($t, 0);
            $rowTotal[] = $this->formatData($v);
            $grandTotalInfo += $v;
        }
        $rowTotal[] = $this->formatData($grandTotalInfo);
        $rows[] = $rowTotal;

        // Keterangan
        $rows[] = [];
        $rows[] = ['* Satuan: Hektar (Ha)  |  Periode: Januari – Desember'];

        return $rows;
    }

    /* ── STYLES ───────────────────────────────────────────────────────────── */
    public function styles(Worksheet $sheet): array
    {
        $nT        = count($this->tahunList);
        $lastColI  = $nT + 2 + 1; // +1 untuk TOTAL
        $lastCol   = Coordinate::stringFromColumnIndex($lastColI);
        $lastTahunCol = Coordinate::stringFromColumnIndex($lastColI - 1); // = tahun terakhir

        $nKab      = Kabupaten::count();
        $r1=1; $r2=2; $r3=3; $r4=4;
        $dS=5; $dE=$dS+$nKab-1;
        $fR=$dE+1;   // footer Sumatera Selatan
        $nR=$fR+2;   // keterangan

        $sheet->getParent()->getDefaultStyle()->getFont()->setName(self::FONT);

        // ── Merge ──────────────────────────────────────────────────────────
        // Judul & sub judul — full lebar
        $sheet->mergeCells("A{$r1}:{$lastCol}{$r1}");
        $sheet->mergeCells("A{$r2}:{$lastCol}{$r2}");

        // Header — No & Kabupaten merge 2 baris
        $sheet->mergeCells("A{$r3}:A{$r4}");
        $sheet->mergeCells("B{$r3}:B{$r4}");

        if ($nT >= 1) {
            $sheet->mergeCells("C{$r3}:{$lastTahunCol}{$r3}");
        }
        $sheet->mergeCells("{$lastCol}{$r3}:{$lastCol}{$r4}");
        $sheet->setCellValue("{$lastCol}{$r3}", 'TOTAL');

        // FIX: Footer "SUMATERA SELATAN" merge A:B
        $sheet->mergeCells("A{$fR}:B{$fR}");

        // ── Baris 1: Judul ─────────────────────────────────────────────────
        $sheet->getStyle("A{$r1}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>14,'name'=>self::FONT,'color'=>['rgb'=>self::TITLE_TEXT]],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::TITLE_BG]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension($r1)->setRowHeight(30);

        // ── Baris 2: Sub judul ─────────────────────────────────────────────
        $sheet->getStyle("A{$r2}")->applyFromArray([
            'font'      => ['italic'=>true,'size'=>10,'name'=>self::FONT,'color'=>['rgb'=>self::SUB_TEXT]],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::SUB_BG]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension($r2)->setRowHeight(18);

        // ── Baris 3: Header-1 (H1_BG) ─────────────────────────────────────
        $sheet->getStyle("A{$r3}:{$lastCol}{$r3}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>10,'name'=>self::FONT,'color'=>['rgb'=>self::H1_TEXT]],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::H1_BG]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension($r3)->setRowHeight(26);

        // ── Baris 4: Header-2 / label tahun (H2_BG) ───────────────────────
        $sheet->getStyle("A{$r4}:{$lastCol}{$r4}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>9,'name'=>self::FONT,'color'=>['rgb'=>self::H2_TEXT]],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::H2_BG]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::BORDER_HEADER]]],
        ]);
        $sheet->getRowDimension($r4)->setRowHeight(18);

        // ── Data rows ───────────────────────────────────────────────────────
        for ($r = $dS; $r <= $dE; $r++) {
            $bg = (($r - $dS) % 2 === 0) ? self::STRIPE_ODD : self::STRIPE_EVEN;
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'fill'    => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
                'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>self::BORDER_DATA]]],
                'font'    => ['name'=>self::FONT,'size'=>9,'color'=>['rgb'=>'374151']],
            ]);
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B{$r}")->getFont()->setBold(true);
            $sheet->getStyle("C{$r}:{$lastCol}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension($r)->setRowHeight(16);
        }

        // ── Footer SUMATERA SELATAN ─────────────────────────────────────────
        $sheet->getStyle("A{$fR}:{$lastCol}{$fR}")->applyFromArray([
            'font'      => ['bold'=>true,'size'=>10,'name'=>self::FONT,'color'=>['rgb'=>self::TOTAL_TEXT]],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>self::TOTAL_BG]],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'14532D']]],
        ]);
        // Label di A (merge A:B) → center
        $sheet->getStyle("A{$fR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Angka rata kanan
        $sheet->getStyle("C{$fR}:{$lastCol}{$fR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($fR)->setRowHeight(22);

        // ── Keterangan ──────────────────────────────────────────────────────
        $sheet->getStyle("A{$nR}")->applyFromArray([
            'font' => ['italic'=>true,'size'=>8,'name'=>self::FONT,'color'=>['rgb'=>'9CA3AF']],
        ]);
        $sheet->getRowDimension($nR)->setRowHeight(14);

        $sheet->freezePane('C5');

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
        $widths = ['A'=>5, 'B'=>30];
        $cols   = array_map(
            fn($i) => Coordinate::stringFromColumnIndex($i),
            range(3, count($this->tahunList) + 2)
        );
        foreach ($cols as $col) {
            $widths[$col] = 16;
        }
        $widths[Coordinate::stringFromColumnIndex(count($this->tahunList) + 3)] = 18; // TOTAL col
        return $widths;
    }
}