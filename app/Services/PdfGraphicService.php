<?php
namespace App\Services;

class PdfGraphicService
{
    public static function prepareData($data, $kolom, $mode, $judulPdf, $chartColors, $kolomKey = null, $useDecimals = false, $headerColor1 = '#111827', $headerColor2 = null, $chartMode = null)
    {
        $dec   = !empty($useDecimals) ? 2 : 0;
        $nKol  = count($kolom);
        $nData = count($data);
        $isBul = ($mode === 'bulanan');
        $isTah = ($mode === 'tahunan');
        $isHar = ($mode === 'harian');
        $h2    = $headerColor2 ?? $headerColor1;

        $isIp  = str_contains(strtolower($judulPdf ?? ''), 'indeks pertanaman');
        $isLbs = str_contains(strtolower($judulPdf ?? ''), 'luas baku sawah');

        if ($chartMode === 'stacked') {
            $isGrouped = false;
        } elseif ($chartMode === 'grouped') {
            $isGrouped = true;
        } else {
            if ($isIp || $isLbs) {
                $isGrouped = true;
            } else {
                $isGrouped = $isTah;
            }
        }

        if ($isTah) {
            $xItems   = array_column($data, 'kabupaten');
            $nX       = $nData;
            $datasets = [];
            foreach ($kolom as $bi => $col) {
                $key  = (!empty($kolomKey) && isset($kolomKey[$bi])) ? $kolomKey[$bi] : $col;
                $vals = [];
                foreach ($data as $row) $vals[] = (float)($row[$key] ?? 0);
                $datasets[] = ['label'=>$col, 'color'=>$chartColors[$bi % count($chartColors)], 'data'=>$vals];
            }
        } else {
            $xItems   = $kolom;
            $nX       = $nKol;
            $datasets = [];
            foreach ($data as $di => $row) {
                $vals = [];
                foreach ($kolom as $bi => $col) {
                    $key    = (!empty($kolomKey) && isset($kolomKey[$bi])) ? $kolomKey[$bi] : $col;
                    $vals[] = (float)($row[$key] ?? 0);
                }
                $datasets[] = ['label'=>$row['kabupaten'], 'color'=>$chartColors[$di % count($chartColors)], 'data'=>$vals];
            }
        }

        $xTotals = [];
        foreach ($xItems as $xi => $xl) {
            $s = 0;
            foreach ($datasets as $ds) $s += $ds['data'][$xi] ?? 0;
            $xTotals[$xi] = $s;
        }

        $allValsFlat = [];
        foreach ($datasets as $ds) foreach ($ds['data'] as $v) $allValsFlat[] = $v;
        $allTotals = array_values($xTotals);

        $rawMax = $isGrouped
            ? (count($allValsFlat) ? (float)max($allValsFlat) : 0)
            : (count($allTotals)   ? (float)max($allTotals)   : 0);

        $hasData = $rawMax > 0;
        $nDS     = count($datasets);

        if ($rawMax > 0) {
            if ($isIp && $rawMax <= 8) {
                $step = ceil($rawMax / 7 * 10) / 10;
                if ($step <= 0) $step = 0.5;
            } else {
                $step = max(1, (int)ceil($rawMax / 7));
            }
            $yMax  = $step * 8;
            $TICKS = 8;
        } else {
            $step = 1; $yMax = 8; $TICKS = 8;
        }

        $CHART_H       = 140;
        $CHART_INNER_H = 125;
        $yColW = '18px';

        if ($isHar) {
            $barW = max(7, min(18, (int)floor(750 / max(1,$nX)) - 2));
            $slotW = 0;
        } elseif ($isTah) {
            $slotW = max(20, (int)floor(750 / max(1,$nX)));
            $barW  = max(2, (int)floor(($slotW - max(1,$nDS)) / max(1,$nDS)));
        } else {
            $barW = max(14, min(44, (int)floor(550 / max(1,$nX)) - 3));
            $slotW = 0;
        }

        return compact(
            'dec', 'nKol', 'nData', 'mode', 'isBul', 'isTah', 'isHar', 'h2', 'isIp', 'isLbs',
            'isGrouped', 'xItems', 'nX', 'datasets', 'xTotals', 'allValsFlat', 'allTotals',
            'rawMax', 'hasData', 'nDS', 'step', 'yMax', 'TICKS', 'CHART_H', 'CHART_INNER_H',
            'yColW', 'barW', 'slotW'
        );
    }
}
