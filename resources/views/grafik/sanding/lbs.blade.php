@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <div class="bg-gradient-to-br from-teal-500 via-teal-600 to-cyan-700 rounded-2xl shadow-2xl p-7 text-white">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-xl">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black mb-1">Grafik Sanding Luas Baku Sawah</h1>
                            <p class="text-teal-100 text-sm font-medium">
                                Perbandingan LBS
                                @if(count($tahunRange) > 0) {{ $periodeLabel }} @endif
                                • Provinsi Sumatera Selatan
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/15 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20 text-center">
                            <p class="text-teal-100 text-xs font-semibold uppercase">Tahun Dipilih</p>
                            <p class="text-xl font-black">{{ count($tahunRange) }}</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20 text-center">
                            <p class="text-teal-100 text-xs font-semibold uppercase">Kabupaten</p>
                            <p class="text-xl font-black">{{ count($data) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CHECKBOX --}}
        <form method="GET" action="{{ route('grafik.sanding.lbs') }}" class="mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Pilih Tahun yang Ditampilkan</h3>
                        <p class="text-xs text-gray-500">Centang tahun yang ingin ditampilkan (minimal 1 tahun)</p>
                    </div>
                </div>

                {{-- Quick buttons --}}
                <div class="flex flex-wrap gap-2 mb-4 pb-3 border-b border-gray-100">
                    <button type="button" onclick="selectAll()"
                        class="px-3 py-1.5 bg-teal-50 text-teal-700 rounded-lg font-bold text-xs border border-teal-200 hover:bg-teal-100 transition-all">✓
                        Semua</button>
                    <button type="button" onclick="deselectAll()"
                        class="px-3 py-1.5 bg-gray-50 text-gray-700 rounded-lg font-bold text-xs border border-gray-200 hover:bg-gray-100 transition-all">✗
                        Hapus</button>
                    <button type="button" onclick="selectLast5()"
                        class="px-3 py-1.5 bg-cyan-50 text-cyan-700 rounded-lg font-bold text-xs border border-cyan-200 hover:bg-cyan-100 transition-all">⊙
                        5 Terakhir</button>
                </div>

                {{-- Checkbox grid --}}
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 mb-4">
                    @foreach($tahunTersedia as $tahun)
                        <label
                            class="relative flex flex-col items-center justify-center p-2.5 bg-white rounded-xl border-2 border-gray-200 hover:border-teal-400 cursor-pointer transition-all text-center">
                            <input type="checkbox" name="tahun[]" value="{{ $tahun }}" {{ in_array($tahun, $tahunRange) ? 'checked' : '' }} class="peer sr-only">
                            <span
                                class="font-black text-sm text-gray-700 peer-checked:text-teal-700 transition-colors">{{ $tahun }}</span>
                            <div
                                class="absolute top-1 right-1 w-4 h-4 rounded-full bg-teal-500 hidden peer-checked:flex items-center justify-center shadow">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div
                                class="absolute inset-0 rounded-xl border-2 border-teal-500 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity">
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="p-2.5 bg-teal-50 border-l-4 border-teal-500 rounded-r-lg mb-4 text-xs font-bold text-teal-800">
                    <span id="selectedCount">{{ count($tahunRange) }}</span> tahun dipilih
                    <span class="text-teal-600 ml-1" id="selectedYears">
                        @if(count($tahunRange) > 0)({{ implode(', ', $tahunRange) }})@endif
                    </span>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl font-bold text-sm hover:from-teal-600 hover:to-cyan-700 shadow transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Tampilkan Grafik
                    </button>
                </div>
            </div>
        </form>

        {{-- INFO --}}
        <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <div class="w-7 h-7 rounded-lg bg-teal-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm text-teal-800">
                <strong>Satuan:</strong> Hektar (Ha) &nbsp;•&nbsp;
                <strong>Sumber:</strong> Survei lapangan BPS &nbsp;•&nbsp;
                <strong>Periode:</strong> Diperbarui setiap 5-6 tahun sekali
            </p>
        </div>

        @if(count($tahunRange) > 0)

            {{-- CHART --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-black text-white">Luas Baku Sawah — {{ $periodeLabel }}</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="downloadPng('lbsChart','lbs-{{ implode('-', $tahunRange) }}')"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            PNG
                        </button>
                        <a href="{{ route('grafik.sanding.lbs.pdf', ['tahun' => $tahunRange]) }}"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <div style="min-width:900px;">
                            <canvas id="lbsChart" style="height:420px;"></canvas>
                        </div>
                    </div>
                    {{-- Legend --}}
                    <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                        @foreach($tahunRange as $idx => $tahun)
                            <div
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-bold text-gray-700">
                                <span class="w-3 h-3 rounded-sm"
                                    style="background:{{ $chartColors[$idx % count($chartColors)] }}"></span>
                                Tahun {{ $tahun }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
                    <h2 class="text-lg font-black text-white">Tabel Luas Baku Sawah (Ha) — {{ $periodeLabel }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr>
                                
                                <th class="border-2 border-teal-200 px-4 py-3 text-left font-black text-teal-900 bg-teal-50">
                                    Kabupaten/Kota</th>
                                @foreach($tahunRange as $idx => $tahun)
                                    <th class="border-2 border-gray-200 px-4 py-3 text-center font-black text-white"
                                        style="background:{{ $chartColors[$idx % count($chartColors)] }}">
                                        {{ $tahun }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $index => $row)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-teal-50/30' }} hover:bg-teal-50 transition-colors">
                                    
                                    <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900">
                                        {{ $row['kabupaten'] }}</td>
                                    @foreach($tahunRange as $idx => $tahun)
                                        @php $val = $row[$tahun] ?? 0; @endphp
                                        <td class="border border-gray-200 px-4 py-2.5 text-right {{ $val > 0 ? 'text-gray-900 font-medium' : 'text-gray-300' }}">
                                            {{ fmtVal($val) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gradient-to-r from-teal-500 to-cyan-600 text-white">
                                <td class="border border-teal-700 px-4 py-3 font-black">
                                    JUMLAH
                                </td>
                                @foreach($tahunRange as $idx => $tahun)
                                    <td class="border border-teal-700 px-4 py-3 text-right font-bold">
                                        {{ fmtVal($totalPerTahun[$tahun] ?? 0) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        @else
            {{-- Kosong --}}
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center border-2 border-dashed border-gray-200">
                <div class="w-16 h-16 rounded-full bg-gray-100 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Tahun Dipilih</h3>
                <p class="text-sm text-gray-500">Centang minimal 1 tahun di atas lalu klik Tampilkan Grafik.</p>
            </div>
        @endif

    </div>

    @if(count($tahunRange) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        const COLORS = {!! json_encode($chartColors) !!};

        function fmt(v) {
    if (v === null || v === undefined) return '';
    if (v === 0) return '0';
    if (v < 0) return '';
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(v);
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


        /* niceMax: 8 intervals (9 ticks), highest bar max at tick 7, integer intervals */
        function niceMax(rawMax) {
            if (!rawMax || rawMax <= 0) return { max: 8, stepSize: 1 };
            const step = Math.ceil(rawMax / 7) || 1;
            return { max: step * 8, stepSize: step };
        }

        document.addEventListener('DOMContentLoaded', function () {
            const labels     = {!! json_encode(array_column($data, 'kabupaten_short')) !!};
            const tahunRange = {!! json_encode($tahunRange) !!};
            const fullData   = {!! json_encode($data) !!};

            const datasets = tahunRange.map((t, i) => ({
                label: 'Tahun ' + t,
                data: fullData.map(r => r[t] || 0),
                backgroundColor: COLORS[i % COLORS.length] + 'CC',
                borderColor: COLORS[i % COLORS.length],
                borderWidth: 1, borderRadius: 4, maxBarThickness: 48,
                categoryPercentage: 0.8, barPercentage: 0.85,
            }));

            // Angka di atas setiap bar
            const labelPlugin = {
                id: 'lp',
                afterDatasetsDraw(chart) {
                    chart.data.datasets.forEach((ds, di) => {
                        chart.getDatasetMeta(di).data.forEach((bar, idx) => {
                            const v = ds.data[idx]; if (!v || v <= 0) return;
                            const { ctx } = chart; ctx.save();
                            ctx.font = 'bold 7px sans-serif'; ctx.fillStyle = '#111';
                            ctx.textAlign = 'center'; ctx.textBaseline = 'bottom';
                            /* ctx.fillText removed */ ctx.restore();
                        });
                    });
                }
            };

            const allVals = fullData.flatMap(r => tahunRange.map(t => r[t] || 0));
            const maxVal  = Math.max(...allVals, 0);
            const { max: yMax, stepSize } = niceMax(maxVal);

            new Chart(document.getElementById('lbsChart').getContext('2d'), {
                type: 'bar', data: { labels, datasets }, plugins: [],
                options: {
                    responsive: true, maintainAspectRatio: false,
                    layout:{padding:{top:20}},
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.85)',
                            callbacks: {
                                label: ctx => ' ' + ctx.dataset.label + ': ' +
                                    new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(ctx.parsed.y) + ' Ha'
                            }
                        }
                    },
                    scales: {
                        x: { border: { display: true, color: '#9CA3AF' }, grid: { display: false, drawTicks: false }, ticks: { font: { size: 8, weight: '600' }, maxRotation: 45, minRotation: 45 } },
                        y: {
                            beginAtZero: true, border: { display: true, color: '#6B7280' }, grid: { color: '#E5E7EB', drawTicks: false, borderDash: [5, 5] }, max: yMax,
                            ticks: {
                                stepSize,
                                callback: v => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(v),
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        });
        </script>
    @endif

    <script>
    function selectAll()  { document.querySelectorAll('input[name="tahun[]"]').forEach(c=>c.checked=true);  updateCount(); }
    function deselectAll(){ document.querySelectorAll('input[name="tahun[]"]').forEach(c=>c.checked=false); updateCount(); }
    function selectLast5(){
        const cbs = Array.from(document.querySelectorAll('input[name="tahun[]"]'));
        cbs.forEach(c=>c.checked=false);
        cbs.slice(-5).forEach(c=>c.checked=true);
        updateCount();
    }
    function updateCount() {
        const checked = document.querySelectorAll('input[name="tahun[]"]:checked');
        document.getElementById('selectedCount').textContent = checked.length;
        document.getElementById('selectedYears').textContent = checked.length > 0
            ? '(' + Array.from(checked).map(c=>c.value).join(', ') + ')' : '';
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[name="tahun[]"]').forEach(c => c.addEventListener('change', updateCount));
    });
    </script>
@endsection