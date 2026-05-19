<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 8mm 10mm; size: A4 landscape; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Helvetica, Arial, sans-serif; font-size: 8pt; color: #111827; background: #fff; }

.kop       { text-align:center; margin-bottom:6px; padding-bottom:6px; border-bottom-width:3px; border-bottom-style:solid; }
.kop-title { font-size:13pt; font-weight:bold; text-transform:uppercase; }
.kop-sub   { font-size:8pt; color:#374151; margin-top:2px; }

.ibox      { width:100%; border-collapse:collapse; margin-bottom:6px; }
.ibox td   { width:33.33%; padding:6px 10px; border-width:1px; border-style:solid; }
.ilbl      { font-size:6.5pt; font-weight:bold; text-transform:uppercase; }
.ival      { font-size:12pt; font-weight:bold; }

.csec      { margin-bottom:6px; border-width:1px; border-style:solid; padding:5px; }
.cttl      { text-align:center; font-size:8pt; font-weight:bold; padding-bottom:4px; }

.lgd       { text-align:center; margin-top:4px; }
.leg-item  { display:inline-block; margin:1px 4px; font-size:5.5pt; color:#374151; }
.leg-box   { display:inline-block; width:9px; height:6px; margin-right:2px; vertical-align:middle; border-radius:1px; }

.sec-lbl   { font-size:7.5pt; font-weight:bold; padding:3px 8px; border-radius:3px; margin-bottom:4px; }

/* ── HARIAN table ── */
.dt-har        { width:100%; border-collapse:collapse; font-size:5.5pt; table-layout:fixed; }
.dt-har th     { padding:2.5px 1px; font-weight:bold; text-align:center; border:1px solid rgba(0,0,0,0.12); white-space:nowrap; line-height:1.2; }
.dt-har th.kab { text-align:left; padding-left:3px; font-size:5pt; }
.dt-har td     { padding:2px 1px; border:1px solid #E5E7EB; text-align:center; white-space:nowrap; line-height:1.2; }
.dt-har td.kab { text-align:left; font-weight:bold; padding-left:3px; font-size:5pt; line-height:1.25; vertical-align:middle; }
.dt-har td.n   { text-align:right; font-weight:bold; padding-right:1px; }
.dt-har tfoot td       { font-weight:bold; color:#fff; text-align:right; padding:2.5px 1px; border:1px solid rgba(0,0,0,.2); }
.dt-har tfoot td.kab   { text-align:left; padding-left:3px; }

/* ── Bulanan/Tahunan table ── */
.dt        { width:100%; border-collapse:collapse; font-size:7pt; }
.dt th     { padding:4px 3px; font-weight:bold; text-align:center; border:1px solid rgba(0,0,0,0.1); white-space:nowrap; }
.dt th.kab { text-align:left; padding-left:6px; }
.dt td     { padding:2px 3px; border:1px solid #E5E7EB; }
.dt td.kab { text-align:left; font-weight:bold; padding-left:4px; white-space:nowrap; }
.dt td.n   { text-align:right; font-weight:bold; }
.dt tfoot td     { font-weight:bold; color:#fff; text-align:right; padding:4px 3px; border:1px solid rgba(0,0,0,.18); }
.dt tfoot td.kab { text-align:left; padding-left:6px; }

.dot  { display:inline-block; width:5px; height:5px; border-radius:1px; vertical-align:middle; margin-right:2px; }
.ftr  { margin-top:5px; padding-top:3px; border-top:1px solid #E5E7EB; text-align:center; font-size:6pt; color:#9CA3AF; }

.x-axis-row    { width:100%; border-collapse:collapse; table-layout:fixed; margin-top:3px; }
.x-axis-row td { text-align:center; padding:2px 0 0; vertical-align:top; line-height:1.2; overflow:hidden; }
</style>
</head>
<body>

@php
/* ── Deteksi mode ── */
$dec       = !empty($useDecimals) ? 2 : 0;
$nKol      = count($kolom);
$nData     = count($data);
$mode      = $pdfMode  ?? 'bulanan';
$chartMode = $chartMode ?? ($mode === 'tahunan' ? 'grouped' : 'stacked');
$isGrouped = ($chartMode === 'grouped');
$isBul     = ($mode === 'bulanan');
$isTah     = ($mode === 'tahunan');
$isHar     = ($mode === 'harian');
$h2        = $headerColor2 ?? $headerColor1;

$isIp  = str_contains(strtolower($judulPdf ?? ''), 'indeks pertanaman');
$isLbs = str_contains(strtolower($judulPdf ?? ''), 'luas baku sawah');

/* ── Build datasets ── */
if ($isTah) {
    /* Tahunan: X = kabupaten, dataset per tahun/kolom */
    $xItems   = array_column($data, 'kabupaten');
    $nX       = $nData;
    $datasets = [];
    foreach ($kolom as $bi => $col) {
        $key  = (!empty($kolomKey) && isset($kolomKey[$bi])) ? $kolomKey[$bi] : $col;
        $vals = [];
        foreach ($data as $row) $vals[] = (float)($row[$key] ?? 0);
        $datasets[] = ['label' => $col, 'color' => $chartColors[$bi % count($chartColors)], 'data' => $vals];
    }
} else {
    /* Bulanan/Harian: X = kolom, dataset per kabupaten */
    $xItems   = $kolom;
    $nX       = $nKol;
    $datasets = [];
    foreach ($data as $di => $row) {
        $vals = [];
        foreach ($kolom as $bi => $col) {
            $key    = (!empty($kolomKey) && isset($kolomKey[$bi])) ? $kolomKey[$bi] : $col;
            $vals[] = (float)($row[$key] ?? 0);
        }
        $datasets[] = [
            'label' => $row['kabupaten'],
            'color' => $chartColors[$di % count($chartColors)],
            'data'  => $vals,
        ];
    }
}

/* ── X-axis totals (untuk stacked: angka di atas bar) ── */
$xTotals = [];
foreach ($xItems as $xi => $xl) {
    $s = 0;
    foreach ($datasets as $ds) $s += $ds['data'][$xi] ?? 0;
    $xTotals[$xi] = $s;
}

/* ── Hitung rawMax ── */
$allValsFlat = [];
foreach ($datasets as $ds) foreach ($ds['data'] as $v) $allValsFlat[] = $v;
$allTotals = array_values($xTotals);

$rawMax = $isGrouped
    ? (count($allValsFlat) ? (float)max($allValsFlat) : 0)
    : (count($allTotals)   ? (float)max($allTotals)   : 0);

$hasData = $rawMax > 0;
$nDS     = count($datasets);

/* ── Skala Y ── */
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

$valToPx = function($v) use ($yMax, $CHART_INNER_H) {
    $v = (float)$v;
    if ($yMax <= 0 || $v <= 0) return 0;
    return (int)round(($v / $yMax) * $CHART_INNER_H);
};

$yColW = '18px';

/* ── Bar width ── */
if ($isHar) {
    $barW = max(7, min(18, (int)floor(750 / max(1,$nX)) - 2));
} elseif ($isTah) {
    $slotW = max(20, (int)floor(750 / max(1,$nX)));
    $barW  = max(2,  (int)floor(($slotW - max(1,$nDS)) / max(1,$nDS)));
} else {
    $barW = max(14, min(44, (int)floor(550 / max(1,$nX)) - 3));
}

/* ── Number formatter ── */
$short = function($v, $isAxis = false) use ($dec, $isIp) {
    if ($v === null || $v === '' || (float)$v == 0) return '0';
    $v = (float)$v;
    $hasDec = (round($v, 2) != round($v, 0));
    if ($dec > 0 || $hasDec) {
        return number_format($v, 2, ',', '.');
    }
    return number_format((int)round($v), 0, ',', '.');
};

$hasKabDot = !($isTah || $isIp || $isLbs);
@endphp

{{-- ══ KOP ══ --}}
<div class="kop" style="border-bottom-color:{{ $headerColor1 }};">
    <div class="kop-title" style="color:{{ $headerColor1 }};">{{ $judulPdf }}</div>
    <div class="kop-sub">Provinsi Sumatera Selatan &#x2022; Periode: {{ $periodeLabel }}</div>
</div>

{{-- ══ INFO BOX ══ --}}
<table class="ibox">
<tr>
    <td style="background:{{ $sectionBg }};border-color:{{ $sectionBg }};">
        <div class="ilbl" style="color:{{ $sectionColor }};">Kabupaten / Kota</div>
        <div class="ival" style="color:{{ $headerColor1 }};">{{ $nData }}</div>
    </td>
    <td style="background:{{ $sectionBg }};border-color:{{ $sectionBg }};">
        <div class="ilbl" style="color:{{ $sectionColor }};">Periode</div>
        <div class="ival" style="color:{{ $headerColor1 }};">{{ $periodeLabel }}</div>
    </td>
    <td style="background:{{ $sectionBg }};border-color:{{ $sectionBg }};">
        <div class="ilbl" style="color:{{ $sectionColor }};">
            @if($isBul)Sanding Bulanan @elseif($isTah)Sanding Tahunan @else Sanding Harian @endif
            &mdash; {{ $isGrouped ? 'Grouped' : 'Stacked' }}
        </div>
        <div class="ival" style="color:{{ $headerColor1 }};">
            @if($isBul){{ $nKol }} Bulan @elseif($isTah){{ $nKol }} Tahun @else{{ $nKol }} Hari @endif
        </div>
    </td>
</tr>
</table>

{{-- ══ CHART ══ --}}
<div class="csec" style="border-color:{{ $sectionBg }};">
<div class="cttl" style="color:{{ $headerColor1 }};">{{ $judulPdf }} &mdash; {{ $periodeLabel }}
    <span style="font-size:6pt;font-weight:normal;color:#6B7280;margin-left:6px;">({{ $isGrouped ? 'Grouped' : 'Stacked' }})</span>
</div>

<table style="width:96%; margin:0 auto; border-collapse:collapse;">
<tr>
    {{-- Sumbu Y --}}
    <td style="width:{{ $yColW }}; border-right:1.5px solid #6B7280; padding:0; vertical-align:bottom;">
        <div style="position:relative; width:100%; height:{{ $CHART_H }}px;">
            @for($t = 0; $t <= $TICKS; $t++)
                @php $vPx = $valToPx($step * $t); @endphp
                <div style="position:absolute; bottom:{{ $vPx - 3 }}px; right:3px;
                            font-size:4.5pt; font-weight:bold; color:#4B5563;
                            text-align:right; width:100%;">
                    {{ $short($step * $t, true) }}
                </div>
            @endfor
        </div>
    </td>

    {{-- Area Bar --}}
    <td style="vertical-align:bottom; padding:0; height:{{ $CHART_H }}px; border-bottom:1.5px solid #9CA3AF;">
        <div style="position:relative; width:100%; height:{{ $CHART_H }}px; overflow:visible;">

            {{-- Grid lines --}}
            @for($t = 1; $t <= $TICKS; $t++)
                @php $bpx = $valToPx($t * $step); @endphp
                <div style="position:absolute; bottom:{{ $bpx }}px; left:0; right:0;
                            border-top:1px solid #E5E7EB; z-index:0;"></div>
            @endfor

            @for($xi = 0; $xi < $nX; $xi++)
                @php
                    $leftPct = ($xi / max(1,$nX)) * 100;
                    $wPct    = 100 / max(1,$nX);
                @endphp

                @if($isGrouped)
                {{-- ══ GROUPED: angka di ATAS setiap bar ══ --}}
                @php
                    $dsCount = count($datasets);
                    $pad     = $wPct * 0.15;
                    $effW    = $wPct - $pad;
                    $barWPct = $effW / max(1,$dsCount);
                @endphp
                @foreach($datasets as $dsi => $ds)
                    @php
                        $v  = (float)($ds['data'][$xi] ?? 0);
                        $px = $valToPx($v);
                        $bL = $leftPct + ($pad / 2) + ($dsi * $barWPct);
                        $bW = $barWPct * 0.85;
                    @endphp
                    @if($px > 0)
                        {{-- Bar --}}
                        <div style="position:absolute; bottom:0; left:{{ $bL }}%; width:{{ $bW }}%;
                                    height:{{ $px }}px; background:{{ $ds['color'] }};
                                    border-radius:2px 2px 0 0; z-index:2;"></div>
                    @endif
                @endforeach

                @else
                {{-- ══ STACKED: angka TOTAL di atas & angka per-segmen di dalam ══ --}}
                @php
                    $stackSegs = [];
                    $cBottom   = 0;
                    foreach ($datasets as $dsi => $ds) {
                        $v  = (float)($ds['data'][$xi] ?? 0);
                        $px = $valToPx($v);
                        if ($px > 0) {
                            $stackSegs[] = [
                                'color'  => $ds['color'],
                                'v'      => $v,
                                'px'     => $px,
                                'bottom' => $cBottom,
                            ];
                            $cBottom += $px;
                        }
                    }
                    $bL = $leftPct + ($wPct * 0.10);
                    $bW = $wPct * 0.80;
                @endphp
                @if(count($stackSegs) > 0)
                    {{-- Segmen dengan angka di dalam --}}
                    @foreach($stackSegs as $seg)
                    <div style="position:absolute; bottom:{{ $seg['bottom'] }}px;
                                left:{{ $bL }}%; width:{{ $bW }}%;
                                height:{{ $seg['px'] }}px; background:{{ $seg['color'] }};
                                z-index:2; overflow:hidden;">
                    </div>
                    @endforeach
                @endif
                @endif
            @endfor
        </div>
    </td>
</tr>

{{-- Label X --}}
<tr>
    <td style="padding:0; width:{{ $yColW }};"></td>
    <td style="padding:0;">
        <table class="x-axis-row">
        <tr>
        @foreach($xItems as $xi => $xl)
            <td style="width:{{ 100/max(1,$nX) }}%;
                       font-size:{{ $isHar ? '3.5pt' : '5pt' }};
                       font-weight:bold; color:#374151;">
                @if($isHar){{ is_numeric($xl) ? $xl : ($xi+1) }}@else{{ $xl }}@endif
            </td>
        @endforeach
        </tr>
        </table>
    </td>
</tr>
</table>

@if(!$hasData)
<div style="text-align:center;font-size:8pt;color:#d1d5db;padding:6px 0;font-style:italic;">Tidak ada data</div>
@endif

<div class="lgd">
    @foreach($datasets as $ds)
    <span class="leg-item">
        <span class="leg-box" style="background:{{ $ds['color'] }};"></span>{{ $ds['label'] }}
    </span>
    @endforeach
</div>
</div>

{{-- ══ LABEL TABEL ══ --}}
<div class="sec-lbl" style="background:{{ $sectionBg }};color:{{ $sectionColor }};">{{ $judulTabel }}</div>

{{-- ══ TABEL DATA ══ --}}
@if($isHar)
{{-- ── Tabel Harian ── --}}
<table class="dt-har">
<thead>
<tr>
    <th class="kab" style="background:{{ $sectionBg }};color:{{ $sectionColor }};width:14%;white-space:nowrap;">Kabupaten / Kota</th>
    @foreach($kolom as $idx => $col)
        @php $dayNum = is_numeric($col) ? $col : ($idx+1); @endphp
        <th style="width:{{ 80/max(1,count($kolom)) }}%;background:{{ $sectionBg }};color:{{ $sectionColor }};font-size:4pt;">{{ $dayNum }}</th>
    @endforeach
    <th style="width:6%;background:{{ $sectionBg }};color:{{ $sectionColor }};font-size:4pt;">Total</th>
</tr>
</thead>
<tbody>
@foreach($data as $di => $row)
    @php
        $rowTotal = 0;
        foreach ($kolom as $idx => $col) {
            $key = (!empty($kolomKey) && isset($kolomKey[$idx])) ? $kolomKey[$idx] : $col;
            $rowTotal += (float)($row[$key] ?? 0);
        }
    @endphp
    <tr>
        <td class="kab" style="background:{{ $di%2==0 ? '#fff' : '#f9fafb' }};">
            @if($hasKabDot)
            <span style="display:inline-block;width:5px;height:5px;border-radius:1px;
                         vertical-align:middle;margin-right:2px;
                         background:{{ $chartColors[$di%count($chartColors)] }};"></span>
            @endif
            {{ strtoupper($row['kabupaten']) }}
        </td>
        @foreach($kolom as $idx => $col)
            @php $key = (!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $v=(float)($row[$key]??0); @endphp
            <td class="n" style="color:#1F2937;font-size:3.5pt;">{{ $short($v) }}</td>
        @endforeach
        <td class="n" style="background:{{ $sectionBg }};color:{{ $sectionColor }};font-size:3.5pt;font-weight:bold;">
            {{ $short($rowTotal) }}
        </td>
    </tr>
@endforeach
</tbody>
<tfoot>
<tr>
    <td class="kab" style="background:{{ $headerColor1 }};">TOTAL</td>
    @foreach($kolom as $idx => $col)
        @php $key=(!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $tot=(float)($totalPerKolom[$key]??0); @endphp
        <td style="background:{{ $headerColor1 }};font-size:3.5pt;">{{ $short($tot) }}</td>
    @endforeach
    @php $grand=array_sum(array_values($totalPerKolom)); @endphp
    <td style="background:{{ $h2 }};font-weight:900;font-size:3.5pt;">{{ $short((float)$grand) }}</td>
</tr>
</tfoot>
</table>

@elseif($isTah)
{{-- ── Tabel Tahunan (X = kabupaten, kolom = tahun) ── --}}
<table class="dt">
<thead>
<tr>
    <th class="kab" style="background:{{ $sectionBg }};color:{{ $sectionColor }};width:14%;white-space:nowrap;">Kabupaten / Kota</th>
    @foreach($kolom as $idx => $col)
        <th style="background:{{ $chartColors[$idx%count($chartColors)] }};color:#fff;padding:4px;">{{ $col }}</th>
    @endforeach
    <th style="background:{{ $headerColor1 }};color:#fff;padding:4px;white-space:nowrap;">Total</th>
</tr>
</thead>
<tbody>
@foreach($data as $di => $row)
    @php
        $rowTotal = 0;
        foreach ($kolom as $idx => $col) {
            $key = (!empty($kolomKey) && isset($kolomKey[$idx])) ? $kolomKey[$idx] : $col;
            $rowTotal += (float)($row[$key] ?? 0);
        }
    @endphp
    <tr>
        <td class="kab" style="background:{{ $di%2==0?'#fff':'#f9fafb' }};">
            @if($hasKabDot)
            <span class="dot" style="background:{{ $chartColors[$di%count($chartColors)] }}"></span>
            @endif
            {{ strtoupper($row['kabupaten']) }}
        </td>
        @foreach($kolom as $idx => $col)
            @php $key=(!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $v=(float)($row[$key]??0); @endphp
            <td class="n" style="color:#1F2937;">{{ $short($v) }}</td>
        @endforeach
        <td class="n" style="background:{{ $sectionBg }};color:{{ $sectionColor }};font-weight:bold;">
            {{ $short($rowTotal) }}
        </td>
    </tr>
@endforeach
</tbody>
<tfoot>
<tr>
    <td class="kab" style="background:{{ $headerColor1 }};">TOTAL</td>
    @foreach($kolom as $idx => $col)
        @php $key=(!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $tot=(float)($totalPerKolom[$key]??0); @endphp
        <td style="background:{{ $headerColor1 }};">{{ $short($tot) }}</td>
    @endforeach
    @php $grand=array_sum(array_values($totalPerKolom)); @endphp
    <td style="background:{{ $h2 }};font-weight:900;">{{ $short((float)$grand) }}</td>
</tr>
</tfoot>
</table>

@else
{{-- ── Tabel Bulanan (X = bulan, kolom = bulan, row = kabupaten) ── --}}
<table class="dt">
<thead>
<tr>
    <th class="kab" style="background:{{ $sectionBg }};color:{{ $sectionColor }};width:14%;white-space:nowrap;">Kabupaten / Kota</th>
    @foreach($kolom as $idx => $col)
        <th style="background:{{ $sectionBg }};color:{{ $sectionColor }};padding:4px;">{{ $col }}</th>
    @endforeach
    <th style="background:{{ $sectionBg }};color:{{ $sectionColor }};padding:4px;white-space:nowrap;">Total</th>
</tr>
</thead>
<tbody>
@foreach($data as $di => $row)
    @php
        $rowTotal = 0;
        foreach ($kolom as $idx => $col) {
            $key = (!empty($kolomKey) && isset($kolomKey[$idx])) ? $kolomKey[$idx] : $col;
            $rowTotal += (float)($row[$key] ?? 0);
        }
    @endphp
    <tr>
        <td class="kab" style="background:{{ $di%2==0?'#fff':'#f9fafb' }};">
            @if($hasKabDot)
            <span class="dot" style="background:{{ $chartColors[$di%count($chartColors)] }}"></span>
            @endif
            {{ strtoupper($row['kabupaten']) }}
        </td>
        @foreach($kolom as $idx => $col)
            @php $key=(!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $v=(float)($row[$key]??0); @endphp
            <td class="n" style="color:#1F2937;">{{ $short($v) }}</td>
        @endforeach
        <td class="n" style="background:{{ $sectionBg }};color:{{ $sectionColor }};font-weight:bold;">
            {{ $short($rowTotal) }}
        </td>
    </tr>
@endforeach
</tbody>
<tfoot>
<tr>
    <td class="kab" style="background:{{ $headerColor1 }};">TOTAL</td>
    @foreach($kolom as $idx => $col)
        @php $key=(!empty($kolomKey)&&isset($kolomKey[$idx]))?$kolomKey[$idx]:$col; $tot=(float)($totalPerKolom[$key]??0); @endphp
        <td style="background:{{ $headerColor1 }};">{{ $short($tot) }}</td>
    @endforeach
    @php $grand=array_sum(array_values($totalPerKolom)); @endphp
    <td style="background:{{ $h2 }};font-weight:900;">{{ $short((float)$grand) }}</td>
</tr>
</tfoot>
</table>
@endif

<div class="ftr">Sumber: Sistem Rekap Padi Sumatera Selatan &#x2022; Dicetak: {{ $tanggalCetak }}</div>

</body>
</html>