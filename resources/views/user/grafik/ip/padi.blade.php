@extends('layouts.topbar.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="bg-gradient-to-br from-green-500 via-emerald-600 to-teal-700 rounded-2xl shadow-2xl p-7 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black mb-1">Grafik IP Padi</h1>
                        <p class="text-green-100 text-sm font-medium">Indeks Pertanaman • Sanding Tahunan • Provinsi Sumatera Selatan</p>
                    </div>
                </div>
                <div class="bg-white/15 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20 text-center">
                    <p class="text-green-100 text-xs font-semibold uppercase">Kabupaten</p>
                    <p class="text-xl font-black">{{ count($data) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Awal</label>
                <select name="tahun_awal" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-green-400 font-semibold text-sm bg-white min-w-[120px]">
                    @foreach($tahunTersedia as $t)
                        <option value="{{ $t }}" {{ $t == $tahunAwal ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Akhir</label>
                <select name="tahun_akhir" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-green-400 font-semibold text-sm bg-white min-w-[120px]">
                    @foreach($tahunTersedia as $t)
                        <option value="{{ $t }}" {{ $t == $tahunAkhir ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold text-sm hover:from-green-600 hover:to-emerald-700 shadow transition-all">Tampilkan</button>
        </div>
    </form>

    {{-- INFO --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <div class="w-7 h-7 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-sm text-green-800">
            <strong>IP</strong> = Luas Tanam ÷ Luas Baku Sawah &nbsp;•&nbsp;
            IP 1.00 = tanam 1×/tahun &nbsp;•&nbsp; IP 2.00 = tanam 2×/tahun &nbsp;•&nbsp; IP 3.00 = tanam 3×/tahun
        </p>
    </div>

    {{-- CHART --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h2 class="text-lg font-black text-white">IP Padi — {{ $tahunAwal }}-{{ $tahunAkhir }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="downloadPng('ipChart','ip-padi-{{ $tahunAwal }}-{{ $tahunAkhir }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    PNG
                </button>
                <a href="{{ route('user.grafik.ip.padi.pdf', ['tahun_awal'=>$tahunAwal,'tahun_akhir'=>$tahunAkhir]) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto"><div style="min-width:900px;"><canvas id="ipChart" style="height:420px;"></canvas></div></div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                @foreach($tahunRange as $idx => $tahun)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-bold text-gray-700">
                        <span class="w-3 h-3 rounded-sm" style="background:{{ $chartColors[$idx % $chartColorsCount] }}"></span>
                        Tahun {{ $tahun }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-lg font-black text-white">Tabel IP Padi — {{ $tahunAwal }}-{{ $tahunAkhir }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-green-50">
                        <th class="border border-green-200 px-4 py-3 text-left font-black text-green-900 sticky left-0 bg-green-50">Kabupaten/Kota</th>
                        @foreach($tahunRange as $idx => $tahun)
                            <th class="border border-gray-200 px-3 py-3 text-center font-black text-white" style="background:{{ $chartColors[$idx % $chartColorsCount] }}">{{ $tahun }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $row)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-green-50/20' }} hover:bg-green-50 transition-colors">
                            <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900 sticky left-0 {{ $index % 2 == 0 ? 'bg-white' : 'bg-green-50/20' }}">{{ $row['kabupaten'] }}</td>
                            @foreach($tahunRange as $tahun)
                                @php $v = $row[$tahun] ?? 0; @endphp
                                <td class="border border-gray-200 px-3 py-2.5 text-right {{ $v > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ fmtIp($v) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                        <td class="border border-green-700 px-4 py-3 font-black">TOTAL</td>
                        @foreach($tahunRange as $tahun)
                            <td class="border border-green-700 px-3 py-3 text-right font-bold">{{ fmtIp($totalPerTahun[$tahun] ?? 0) }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const COLORS = {!! json_encode($chartColors) !!};
function fmt2(v) {
    if (v === null || v === undefined) return '';
    if (v === 0) return '0';
    if (v < 0) return '';
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(v);
}
function fmtFull2(v) {
    if (v === null || v === undefined) return '';
    if (v === 0) return '0';
    if (v <= 0) return '';
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


document.addEventListener('DOMContentLoaded',function(){
    const labels={!! json_encode(array_column($data,'kabupaten_short')) !!};
    const tr={!! json_encode($tahunRange) !!};
    const fd={!! json_encode($data) !!};
    const ds=tr.map((t,i)=>({label:'Tahun '+t,data:fd.map(r=>r[t]||0),backgroundColor:COLORS[i%COLORS.length]+'CC',borderColor:COLORS[i%COLORS.length],borderWidth:1,borderRadius:4,maxBarThickness:48,categoryPercentage:.8,barPercentage:.85}));
    const av=fd.flatMap(r=>tr.map(t=>r[t]||0)); const mv=Math.max(...av,0);
    const st = mv > 0 ? (Math.ceil((mv / 7) * 10) / 10) : 0.5;
    new Chart(document.getElementById('ipChart').getContext('2d'),{type:'bar',data:{labels,datasets:ds},plugins:[],options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(0,0,0,0.85)',callbacks:{label:ctx=>' '+ctx.dataset.label+': '+fmtFull2(ctx.parsed.y)}}},scales:{x:{border: { display: true, color: '#9CA3AF' }, grid: { display: false, drawTicks: false },ticks:{font:{size:8,weight:'600'},maxRotation:45,minRotation:45}},y:{beginAtZero: true, border: { display: true, color: '#6B7280' }, grid: { color: '#E5E7EB', drawTicks: false, borderDash: [5, 5] },max:st*8,ticks:{stepSize:st,callback:v=>new Intl.NumberFormat('id-ID',{minimumFractionDigits:2}).format(v),font:{size:10}}}}}});
});
</script>
@endsection