@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 rounded-2xl shadow-2xl p-7 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black mb-1">Grafik KSA — Luas Tanam</h1>
                        <p class="text-amber-100 text-sm font-medium">Bulanan (Okt–Sep) • Total Tahunan • Provinsi Sumatera Selatan</p>
                    </div>
                </div>
                <div class="bg-white/15 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20 text-center">
                    <p class="text-amber-100 text-xs font-semibold uppercase">Kabupaten</p>
                    <p class="text-xl font-black">{{ count($kabupatens) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB --}}
    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-1.5 flex gap-1">
            @php $tabs = [
                'bulanan' => ['label'=>'Sanding Bulanan (Okt–Sep)','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                'tahunan' => ['label'=>'Sanding Tahunan','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ]; @endphp
            @foreach($tabs as $key => $t)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                          {{ $tab === $key ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md' : 'text-gray-600 hover:bg-amber-50 hover:text-amber-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/></svg>
                    {{ $t['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ========================= TAB BULANAN ========================= --}}
    @if($tab === 'bulanan')
    <div>
        <form method="GET" class="mb-6">
            <input type="hidden" name="tab" value="bulanan">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Pilih Tahun (Okt-Sep)</label>
                    <select name="tahun_bulanan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-amber-400 font-semibold text-sm bg-white min-w-[150px]">
                        @foreach($tahunTersedia as $t)
                            <option value="{{ $t }}" {{ $tahunBulanan == $t ? 'selected' : '' }}>Okt {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-bold text-sm hover:from-amber-600 hover:to-orange-600 shadow transition-all">Tampilkan</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <h2 class="text-lg font-black text-white">KSA Luas Tanam per Bulan — Okt {{ $tahunBulanan }} s.d. Sep {{ $tahunBulanan + 1 }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Default: Stacked aktif --}}
                    <button onclick="setMode('stacked')" id="btn-stacked" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/30 text-white border-white/40">Stacked</button>
                    <button onclick="setMode('grouped')" id="btn-grouped" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/10 text-white/60 border-white/20">Grouped</button>
                    <button onclick="downloadPng('bulananChart','ksa-tanam-bulanan-{{ $tahunBulanan }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        PNG
                    </button>
                    <a id="pdf-btn-bulanan" href="{{ route('grafik.ksa.tanam.pdf', ['tab'=>'bulanan','tahun_bulanan'=>$tahunBulanan,'chart_mode'=>'stacked']) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto"><div style="min-width:900px;position:relative;"><canvas id="bulananChart" style="height:420px;"></canvas></div></div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                    @foreach($dataBulanan as $i => $row)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700">
                            <span class="w-3 h-3 rounded-sm" style="background:{{ $chartColors[$i % $chartColorsCount] }}"></span>
                            {{ $row['kabupaten'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <h2 class="text-lg font-black text-white">Tabel KSA Luas Tanam Bulanan (Ha) — Okt {{ $tahunBulanan }} s.d. Sep {{ $tahunBulanan + 1 }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-amber-50">
                            <th class="border border-amber-200 px-4 py-3 text-left font-black text-amber-900 sticky left-0 bg-amber-50">Kabupaten/Kota</th>
                            @foreach(['Okt','Nov','Des','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep'] as $bl)
                                <th class="border border-amber-200 px-3 py-3 text-center font-black text-amber-900">{{ $bl }}</th>
                            @endforeach
                            <th class="border border-amber-200 px-3 py-3 text-center font-black text-amber-900 bg-amber-100">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataBulanan as $index => $row)
                            @php $rowTotal = array_sum($row['data']); @endphp
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-amber-50/20' }} hover:bg-amber-50 transition-colors">
                                <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900 sticky left-0 {{ $index % 2 == 0 ? 'bg-white' : 'bg-amber-50/20' }}">
                                    <span class="inline-block w-2.5 h-2.5 rounded-sm mr-1.5" style="background:{{ $chartColors[$index % $chartColorsCount] }}"></span>
                                    {{ $row['kabupaten'] }}
                                </td>
                                @foreach($row['data'] as $val)
                                    <td class="border border-gray-200 px-3 py-2.5 text-right {{ $val > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ fmtVal($val) }}</td>
                                @endforeach
                                <td class="border border-amber-200 px-3 py-2.5 text-right font-bold bg-amber-50 text-amber-800">{{ fmtVal($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                            <td class="border border-amber-700 px-4 py-3 font-black">TOTAL</td>
                            @foreach($totalBulanan as $t)
                                <td class="border border-amber-700 px-3 py-3 text-right font-bold">{{ fmtVal($t) }}</td>
                            @endforeach
                            <td class="border border-amber-700 px-3 py-3 text-right font-black">{{ fmtVal(array_sum($totalBulanan)) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================= TAB TAHUNAN ========================= --}}
    @elseif($tab === 'tahunan')
    <div>
        <form method="GET" class="mb-6">
            <input type="hidden" name="tab" value="tahunan">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Awal</label>
                    <select name="tahun_awal_tahunan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-amber-400 font-semibold text-sm bg-white min-w-[120px]">
                        @foreach($tahunTersedia as $t) <option value="{{ $t }}" {{ $tahunAwalT == $t ? 'selected' : '' }}>{{ $t }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Akhir</label>
                    <select name="tahun_akhir_tahunan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-amber-400 font-semibold text-sm bg-white min-w-[120px]">
                        @foreach($tahunTersedia as $t) <option value="{{ $t }}" {{ $tahunAkhirT == $t ? 'selected' : '' }}>{{ $t }}</option> @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-bold text-sm hover:from-amber-600 hover:to-orange-500 shadow transition-all">Tampilkan</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h2 class="text-lg font-black text-white">Total KSA Luas Tanam per Tahun — {{ $tahunAwalT }}-{{ $tahunAkhirT }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="downloadPng('tahunanChart','ksa-tanam-tahunan-{{ $tahunAwalT }}-{{ $tahunAkhirT }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        PNG
                    </button>
                    <a href="{{ route('grafik.ksa.tanam.pdf', ['tab'=>'tahunan','tahun_awal_tahunan'=>$tahunAwalT,'tahun_akhir_tahunan'=>$tahunAkhirT,'chart_mode'=>'stacked']) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto"><div style="min-width:900px;"><canvas id="tahunanChart" style="height:420px;"></canvas></div></div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                    @foreach($tahunRangeT as $idx => $t)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-bold text-gray-700">
                            <span class="w-3 h-3 rounded-sm" style="background:{{ $chartColors[$idx % $chartColorsCount] }}"></span>
                            Tahun {{ $t }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <h2 class="text-lg font-black text-white">Tabel Total Tahunan (Ha) — {{ $tahunAwalT }}-{{ $tahunAkhirT }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-amber-50">
                            <th class="border border-amber-200 px-4 py-3 text-left font-black text-amber-900">Kabupaten/Kota</th>
                            @foreach($tahunRangeT as $idx => $t)
                                <th class="border border-gray-200 px-3 py-3 text-center font-black text-white" style="background:{{ $chartColors[$idx % $chartColorsCount] }}">{{ $t }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataTahunan as $index => $row)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-amber-50/20' }} hover:bg-amber-50 transition-colors">
                                <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900">{{ $row['kabupaten'] }}</td>
                                @foreach($tahunRangeT as $t)
                                    @php $v = $row[$t] ?? 0; @endphp
                                    <td class="border border-gray-200 px-3 py-2.5 text-right {{ $v > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ fmtVal($v) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                            <td class="border border-amber-700 px-4 py-3 font-black">TOTAL</td>
                            @foreach($tahunRangeT as $t)
                                <td class="border border-amber-700 px-3 py-3 text-right font-bold">{{ fmtVal($totalTahunan[$t] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const COLORS = {!! json_encode($chartColors) !!};

function fmt(v) {
    if (v === null || v === undefined || v <= 0) return '';
    let isD = Math.round(v*100)/100 !== Math.round(v);
    if (isD) return new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(v);
    return new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Math.round(v));
}
function fmtFull(v) {
    let isD = Math.round(v*100)/100 !== Math.round(v);
    if (isD) return new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(v);
    return new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Math.round(v));
}

function downloadPng(id, name) {
    const canvas = document.getElementById(id);
    if (!canvas) return;
    let titleText = name.replace(/-/g, ' ').toUpperCase();
    const container = canvas.closest('.bg-white');
    if (container) { const h2 = container.querySelector('h2'); if (h2) titleText = h2.innerText; }
    
    const chart = Chart.getChart(id);
    if (!chart) return;
    
    const fmt = v => {
        if (!v || v<=0) return '';
        let isD = Math.round(v*100)/100 !== Math.round(v);
        if (isD) return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2, maximumFractionDigits:2}).format(v);
        return new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Math.round(v));
    };

    const cTemp = document.createElement('canvas');
    const ctxTemp = cTemp.getContext('2d');
    ctxTemp.font = 'bold 11px sans-serif';
    
    let items = [];
    if (chart.data.datasets) {
        items = chart.data.datasets.map(ds => {
            const w = ctxTemp.measureText(ds.label).width + 25; 
            let color = ds.backgroundColor;
            if(Array.isArray(color)) color = color[0];
            return { label: ds.label, color: color || '#333', width: w };
        });
    }
    
    // Scale pl / pt up to native pixel equivalent natively if needed
    // However, canvas.width is native pixels!
    const dpr = window.devicePixelRatio || 1;
    const pl = 30 * dpr, pr = 30 * dpr, pt = 70 * dpr;
    const maxWidth = canvas.width + pl + pr - 40 * dpr;
    
    let lines = []; let curLine = []; let curW = 0;
    items.forEach(item => {
        if (curW + item.width*dpr > maxWidth && curLine.length > 0) {
            lines.push({ items: curLine, width: curW });
            curLine = [item]; curW = item.width*dpr;
        } else {
            curLine.push(item); curW += item.width*dpr;
        }
    });
    if (curLine.length > 0) lines.push({ items: curLine, width: curW });
    
    const pb = (40 + lines.length * 20) * dpr; 
    
    const c = document.createElement('canvas');
    const ctx = c.getContext('2d');
    
    c.width = canvas.width + pl + pr;
    c.height = canvas.height + pt + pb;
    
    // Background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, c.width, c.height);
    
    // Title
    ctx.fillStyle = '#111827';
    ctx.font = 'bold ' + (17 * dpr) + 'px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    ctx.fillText(titleText, c.width / 2, 25 * dpr);
    
    // Original chart native draw
    ctx.drawImage(canvas, pl, pt);
    
    // Exact Coordinate mapping
    const scaleX = canvas.width / (chart.width || canvas.clientWidth || 1);
    const scaleY = canvas.height / (chart.height || canvas.clientHeight || 1);
    
    const isStacked = chart.options?.scales?.x?.stacked === true;
    let totals = {}; 
    let allY = [];
    
    chart.data.datasets.forEach((ds, di) => {
        const meta = chart.getDatasetMeta(di);
        meta.data.forEach((bar, idx) => {
            const v = ds.data[idx];
            if (!v || v <= 0) return;
            allY.push(bar.y * scaleY + pt);
        });
    });
    
    chart.data.datasets.forEach((ds, di) => {
        const meta = chart.getDatasetMeta(di);
        meta.data.forEach((bar, idx) => {
            const v = ds.data[idx];
            if (!v || v <= 0) return;
            
            const x = (bar.x * scaleX) + pl;
            const y = (bar.y * scaleY) + pt;
            let base = pt;
            if (bar.base !== undefined) {
                base = (bar.base * scaleY) + pt;
            } else {
                base = (chart.scales.y.getPixelForValue(0) * scaleY) + pt;
            }
            const barH = Math.abs(base - y);
            
            if (isStacked) {
                if (!totals[idx]) totals[idx] = { sum: 0, topY: 999999, x: x };
                totals[idx].sum += v;
                if (y < totals[idx].topY) totals[idx].topY = y;
                
                // Print inside if it fits (like PDF)
                if (true) {
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold ' + (10 * dpr) + 'px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    /* ctx.fillText hidden */
                }
            } else {
                // Grouped (PDF Style)
                ctx.fillStyle = '#111827';
                ctx.font = 'bold ' + (10 * dpr) + 'px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                
                if (y - (3 * dpr) < pt) {
                     ctx.fillStyle = '#ffffff';
                     ctx.textBaseline = 'top';
                     /* ctx.fillText hidden */
                } else {
                     /* ctx.fillText hidden */
                }
            }
        });
    });
    
    if (isStacked) {
        Object.keys(totals).forEach(idx => {
            const tot = totals[idx];
            if (tot.sum > 0) {
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold ' + (11 * dpr) + 'px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                
                if (tot.topY - (4 * dpr) < pt) {
                     ctx.fillStyle = '#ffffff';
                     ctx.textBaseline = 'top';
                     /* ctx.fillText hidden */
                } else {
                     /* ctx.fillText hidden */
                }
            }
        });
    }
    
    // Draw Legend
    ctx.font = 'bold ' + (11 * dpr) + 'px sans-serif';
    let legendY = c.height - pb + (20 * dpr);
    
    lines.forEach(line => {
        let startX = (c.width - line.width) / 2;
        line.items.forEach(item => {
            ctx.fillStyle = item.color;
            ctx.fillRect(startX, legendY - (5 * dpr), 12 * dpr, 12 * dpr); 
            ctx.fillStyle = '#374151';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';
            ctx.fillText(item.label, startX + (18 * dpr), legendY + (1 * dpr));
            startX += item.width * dpr;
        });
        legendY += 20 * dpr;
    });
    
    const a = document.createElement('a');
    a.download = name + '.png';
    a.href = c.toDataURL('image/png', 1.0);
    a.click();
}


function niceMax(rawMax) {
    if (!rawMax || rawMax <= 0) return {max:8, stepSize:1};
    const step = Math.ceil(rawMax/7) || 1;
    return {max: step*8, stepSize: step};
}

const btnOn  = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/30 text-white border-white/40';
const btnOff = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/10 text-white/60 border-white/20';

function syncPdfBtn(btnId, mode) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const url = new URL(btn.href);
    url.searchParams.set('chart_mode', mode);
    btn.href = url.toString();
}

@if($tab === 'bulanan')
const bulData  = {!! json_encode($dataBulanan) !!};
const bulTotal = {!! json_encode(array_values($totalBulanan)) !!};
const bulLabels= {!! json_encode($bulanLabels) !!};
let bulChart;

function buildDs(mode) {
    return bulData.map((row,i) => ({
        label: row.kabupaten, data: row.data,
        backgroundColor: COLORS[i%COLORS.length]+(mode==='stacked'?'E0':'CC'),
        borderColor: COLORS[i%COLORS.length],
        borderWidth:1.5, borderRadius: mode==='stacked'?0:3,
        maxBarThickness: mode==='stacked'?38:20,
        categoryPercentage: mode==='stacked'?0.8:0.9,
        barPercentage: mode==='stacked'?0.95:0.85,
        stack: mode==='stacked'?'s':row.kabupaten,
    }));
}

function makeBulChart(mode) {
    if (bulChart) bulChart.destroy();
    const rawMax = mode==='grouped' ? Math.max(...bulData.flatMap(r=>r.data),0) : Math.max(...bulTotal,0);
    const {max:yMax, stepSize} = niceMax(rawMax);

    const totalPlugin = {id:'tot', afterDatasetsDraw(chart) {
        if (mode!=='stacked') return;
        const {ctx,scales:{x,y}} = chart; ctx.save();
        bulLabels.forEach((_,i) => {
            const v = bulTotal[i]; if(!v||v<=0) return;
            ctx.font='bold 9px sans-serif'; ctx.fillStyle='#111'; ctx.textAlign='center'; ctx.textBaseline='bottom';
            /* ctx.fillText removed */ });
        ctx.restore();
    }};

    bulChart = new Chart(document.getElementById('bulananChart').getContext('2d'), {
        type:'bar', data:{labels:bulLabels, datasets:buildDs(mode)}, plugins:[totalPlugin],
        options:{
            responsive:true, maintainAspectRatio:false,
            layout:{padding:{top:20}},
            plugins:{
                legend:{display:false},
                
                tooltip:{backgroundColor:'rgba(0,0,0,0.85)', callbacks:{
                    label: ctx=>' '+ctx.dataset.label+': '+fmtFull(ctx.parsed.y)+' Ha',
                    afterBody: ctx=>[' ─────────────',' Total: '+fmtFull(bulTotal[ctx[0].dataIndex])+' Ha']
                }}
            },
            scales:{
                x:{stacked:mode==='stacked', border:{display:true,color:'#9CA3AF'}, grid:{display:false,drawTicks:false}, ticks:{font:{size:11,weight:'700'},color:'#374151'}},
                y:{stacked:mode==='stacked', beginAtZero:true, border:{display:true,color:'#6B7280'}, grid:{color:'#E5E7EB',drawTicks:false,borderDash:[5,5]}, max:yMax, ticks:{stepSize,callback:v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v),font:{size:10}}}
            }
        }
    });
}

function setMode(m) {
    document.getElementById('btn-stacked').className = m==='stacked'?btnOn:btnOff;
    document.getElementById('btn-grouped').className = m==='grouped'?btnOn:btnOff;
    syncPdfBtn('pdf-btn-bulanan', m);
    makeBulChart(m);
}

/* ── Default awal: STACKED ── */
document.addEventListener('DOMContentLoaded', () => setMode('stacked'));

@elseif($tab === 'tahunan')
document.addEventListener('DOMContentLoaded', function() {
    const labels = {!! json_encode(array_column($dataTahunan,'kabupaten')) !!};
    const tr = {!! json_encode($tahunRangeT) !!};
    const fd = {!! json_encode($dataTahunan) !!};
    const ds = tr.map((t,i) => ({
        label:'Tahun '+t, data:fd.map(r=>r[t]||0),
        backgroundColor:COLORS[i%COLORS.length]+'CC', borderColor:COLORS[i%COLORS.length],
        borderWidth:1, borderRadius:4, maxBarThickness:48, categoryPercentage:.8, barPercentage:.85
    }));
    const mv = Math.max(...fd.flatMap(r=>tr.map(t=>r[t]||0)),0);
    const {max:yMaxT, stepSize:stT} = niceMax(mv);
    const lp = {id:'lp', afterDatasetsDraw(chart) {
        chart.data.datasets.forEach((d,di)=>chart.getDatasetMeta(di).data.forEach((bar,idx)=>{
            const v=d.data[idx]; if(!v||v<=0) return;
            const{ctx}=chart; ctx.save();
            ctx.font='bold 7px sans-serif'; ctx.fillStyle='#111'; ctx.textAlign='center'; ctx.textBaseline='bottom';
            /* ctx.fillText removed */ ctx.restore();
        }));
    }};
    new Chart(document.getElementById('tahunanChart').getContext('2d'), {
        type:'bar', data:{labels,datasets:ds}, plugins:[],
        options:{
            responsive:true, maintainAspectRatio:false,
            layout:{padding:{top:20}},
            plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(0,0,0,0.85)',callbacks:{label:ctx=>' '+ctx.dataset.label+': '+fmtFull(ctx.parsed.y)+' Ha'}}},
            scales:{
                x:{border:{display:true,color:'#9CA3AF'},grid:{display:false,drawTicks:false},ticks:{font:{size:8,weight:'600'},maxRotation:45,minRotation:45}},
                y:{beginAtZero:true,border:{display:true,color:'#6B7280'},grid:{color:'#E5E7EB',drawTicks:false,borderDash:[5,5]},max:yMaxT,ticks:{stepSize:stT,callback:v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v),font:{size:10}}}
            }
        }
    });
});
@endif
</script>
@endsection