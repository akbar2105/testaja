@extends('layouts.topbar.app')
@section('title', 'Dashboard')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
.dash-root { font-family: 'Plus Jakarta Sans', sans-serif; }

.hero-banner {
    background: linear-gradient(135deg, #052e16 0%, #14532d 35%, #166534 65%, #15803d 100%);
    position: relative; overflow: hidden;
}
.hero-banner::before {
    content: ''; position: absolute; inset: 0;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(134,239,172,0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(74,222,128,0.10) 0%, transparent 40%);
    pointer-events: none;
}
.hero-ring {
    position: absolute; right: 40px; bottom: -80px;
    width: 220px; height: 220px; border-radius: 50%;
    border: 40px solid rgba(255,255,255,0.04); pointer-events: none;
}
.hero-ring2 {
    position: absolute; right: -60px; top: -60px;
    width: 320px; height: 320px; border-radius: 50%;
    background: rgba(255,255,255,0.03); pointer-events: none;
}
.stat-card {
    background: #fff; border-radius: 20px;
    border: 1.5px solid #f1f5f9;
    box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,.08), 0 20px 40px rgba(0,0,0,.06); }
.stat-card-bar { height: 3px; width: 100%; }
.section-card {
    background: #fff; border-radius: 20px;
    border: 1.5px solid #f1f5f9;
    box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 8px 24px rgba(0,0,0,.04);
    overflow: hidden;
}
.section-header {
    padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    background: #fafafa;
}
.quick-item {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 16px 10px; border-radius: 16px; border: 1.5px solid transparent;
    transition: all .2s ease; text-decoration: none;
}
.quick-item:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }
.recent-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #f8fafc; transition: background .15s;
}
.recent-item:last-child { border-bottom: none; }
.recent-item:hover { background: #fafafa; }
.grafik-item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; border-radius: 14px; border: 1.5px solid #f1f5f9;
    transition: all .2s; text-decoration: none;
}
.grafik-item:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.05); }
.shortcut-link {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 18px; border-radius: 18px; border: 1.5px solid;
    text-decoration: none; transition: all .2s;
}
.shortcut-link:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
</style>

<div class="dash-root space-y-5 pb-6">

    {{-- HERO --}}
    <div class="hero-banner rounded-2xl px-7 py-8 text-white">
        <div class="hero-ring"></div>
        <div class="hero-ring2"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl text-3xl"
                     style="background:rgba(255,255,255,.12); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2);">🌾</div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Rekap Padi Sumsel</h1>
                    <p class="mt-1 text-sm text-green-200 font-medium">Sumatera Selatan &nbsp;·&nbsp; {{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>
            <a href="{{ route('map') }}"
               class="inline-flex items-center gap-2 self-start sm:self-auto rounded-xl px-4 py-2.5 text-sm font-semibold transition-all hover:bg-white/20"
               style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c-.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                </svg>
                Peta Sawah
            </a>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="stat-card">
            <div class="stat-card-bar" style="background:linear-gradient(90deg,#3b82f6,#6366f1);"></div>
            <div class="p-5 flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:#eff6ff;">
                    <svg class="h-6 w-6" style="color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Kabupaten / Kota</p>
                    <p class="mt-0.5 text-3xl font-bold text-gray-900">{{ $totalKabupaten }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Wilayah terdaftar</p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-bar" style="background:linear-gradient(90deg,#22c55e,#16a34a);"></div>
            <div class="p-5 flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:#f0fdf4;">
                    <svg class="h-6 w-6" style="color:#22c55e;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Luas Tanam</p>
                    <p class="mt-0.5 text-3xl font-bold text-gray-900">{{ number_format($totalRekapTanam, 0, ',', '.') }}<span class="text-base ml-1 text-gray-500 font-medium">Ha</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Tahun {{ date('Y') }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-bar" style="background:linear-gradient(90deg,#f59e0b,#d97706);"></div>
            <div class="p-5 flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:#fffbeb;">
                    <svg class="h-6 w-6" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Luas Panen</p>
                    <p class="mt-0.5 text-3xl font-bold text-gray-900">{{ number_format($totalRekapPanen, 0, ',', '.') }}<span class="text-base ml-1 text-gray-500 font-medium">Ha</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Tahun {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- AKSES CEPAT --}}
    <div class="section-card">
        <div class="section-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Akses Cepat</h3>
                <p class="text-xs text-gray-400 mt-0.5">Langsung ke fitur yang paling sering dipakai</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                @php
                $quickMenus = [
                    ['label'=>'Rekap Tanam', 'route'=>'user.rekap.harian.tanam.index', 'emoji'=>'🌱', 'bg'=>'#f0fdf4', 'border'=>'#bbf7d0', 'text'=>'#166534'],
                    ['label'=>'Rekap Panen', 'route'=>'user.rekap.harian.panen.index', 'emoji'=>'🌾', 'bg'=>'#fffbeb', 'border'=>'#fde68a', 'text'=>'#92400e'],
                    ['label'=>'IP Padi',     'route'=>'user.ip.padi.index',            'emoji'=>'🌿', 'bg'=>'#f0fdfa', 'border'=>'#99f6e4', 'text'=>'#115e59'],
                    ['label'=>'LBS',         'route'=>'user.lbs.index',                'emoji'=>'🗾', 'bg'=>'#f0fdfa', 'border'=>'#5eead4', 'text'=>'#0f766e'],
                    ['label'=>'Peta Sawah',  'route'=>'map',                           'emoji'=>'🗺️', 'bg'=>'#f8fafc', 'border'=>'#e2e8f0', 'text'=>'#475569'],
                ];
                @endphp
                @foreach($quickMenus as $m)
                <a href="{{ route($m['route']) }}" class="quick-item"
                   style="background:{{ $m['bg'] }}; border-color:{{ $m['border'] }};">
                    <span class="text-2xl">{{ $m['emoji'] }}</span>
                    <span class="text-xs font-semibold text-center leading-tight" style="color:{{ $m['text'] }};">{{ $m['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- GRAFIK INTERAKTIF INLINE --}}
    <div class="section-card">
        <div class="section-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Pergerakan Luas Tanam &amp; Panen {{ date('Y') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Visibilitas data tren secara *real-time* sepanjang tahun</p>
            </div>
        </div>
        <div class="p-5">
            <div id="inlineChartContainer" style="height: 350px; width:100%; border-radius: 12px; overflow:hidden;"></div>
        </div>
    </div>

    {{-- DATA TERBARU --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        <div class="section-card">
            <div class="section-header">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:#dcfce7;">
                        <svg class="h-4 w-4" style="color:#16a34a;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Data Tanam Terbaru</p>
                        <p class="text-xs text-gray-400">5 rekap terkini</p>
                    </div>
                </div>
                <a href="{{ route('user.rekap.bulanan.tanam.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white"
                   style="background:#16a34a;">
                    Semua
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            <div>
                @forelse($recentTanam as $i => $tanam)
                <div class="recent-item">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-xs font-bold" style="background:#f0fdf4; color:#16a34a;">#{{ $i+1 }}</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $tanam->kabupaten->nama_kabupaten ?? '—' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $tanam->tahun }} &nbsp;·&nbsp; <span class="font-semibold text-green-600">{{ number_format($tanam->total, 2, ',', '.') }} Ha</span></p>
                        </div>
                    </div>
                    <a href="{{ route('user.rekap.bulanan.tanam.show', $tanam) }}" class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg text-white" style="background:#16a34a;">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <span class="text-3xl mb-2">🌱</span>
                    <p class="text-sm text-gray-400 font-medium">Belum ada data tanam</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:#fef3c7;">
                        <svg class="h-4 w-4" style="color:#d97706;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Data Panen Terbaru</p>
                        <p class="text-xs text-gray-400">5 rekap terkini</p>
                    </div>
                </div>
                <a href="{{ route('user.rekap.bulanan.panen.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white"
                   style="background:#d97706;">
                    Semua
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            <div>
                @forelse($recentPanen as $i => $panen)
                <div class="recent-item">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-xs font-bold" style="background:#fffbeb; color:#b45309;">#{{ $i+1 }}</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $panen->kabupaten->nama_kabupaten ?? '—' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $panen->tahun }} &nbsp;·&nbsp; <span class="font-semibold text-yellow-600">{{ number_format($panen->total, 2, ',', '.') }} Ha</span></p>
                        </div>
                    </div>
                    <a href="{{ route('user.rekap.bulanan.panen.show', $panen) }}" class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg text-white" style="background:#d97706;">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <span class="text-3xl mb-2">🌾</span>
                    <p class="text-sm text-gray-400 font-medium">Belum ada data panen</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- GRAFIK & ANALISIS --}}
    <div class="section-card">
        <div class="section-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">📈 Grafik &amp; Analisis</h3>
                <p class="text-xs text-gray-400 mt-0.5">Visualisasi data pertanian Sumatera Selatan</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                @php
                $grafikMenus = [
                    ['label'=>'Grafik Luas Tanam',     'sub'=>'Perbandingan LTT tahunan', 'route'=>'user.grafik.ltt.tanam',    'emoji'=>'📈'],
                    ['label'=>'Grafik Luas Panen',     'sub'=>'Perbandingan LTP tahunan', 'route'=>'user.grafik.ltt.panen',    'emoji'=>'📊'],
                    ['label'=>'Grafik KSA Luas Tanam', 'sub'=>'Sanding KSA per tahun',    'route'=>'user.grafik.ksa.tanam',    'emoji'=>'🌱'],
                    ['label'=>'Grafik KSA Luas Panen', 'sub'=>'Sanding KSA per tahun',    'route'=>'user.grafik.ksa.panen',    'emoji'=>'🌾'],
                    ['label'=>'Grafik KSA Produksi',   'sub'=>'Produksi padi per tahun',  'route'=>'user.grafik.ksa.produksi', 'emoji'=>'🏭'],
                    ['label'=>'Grafik IP Padi',        'sub'=>'Indeks pertanaman padi',   'route'=>'user.grafik.ip.padi',      'emoji'=>'🌿'],
                ];
                @endphp
                @foreach($grafikMenus as $g)
                <a href="{{ route($g['route']) }}" class="grafik-item group">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-lg" style="background:#f8fafc;">{{ $g['emoji'] }}</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $g['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ $g['sub'] }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 text-gray-300 flex-shrink-0 group-hover:text-gray-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SHORTCUT — KSA TANAM · KSA PANEN · KSA PRODUKSI --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('user.ksa.tanam.bulanan.index') }}" class="shortcut-link" style="background:#fff7ed; border-color:#fed7aa;">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl text-xl text-white" style="background:#ea580c;">📊</div>
            <div>
                <p class="font-bold text-sm" style="color:#7c2d12;">KSA Luas Tanam</p>
                <p class="text-xs mt-0.5" style="color:#fb923c;">Sanding bulanan per tahun</p>
            </div>
            <svg class="ml-auto h-5 w-5 flex-shrink-0" style="color:#fdba74;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
        <a href="{{ route('user.ksa.panen.bulanan.index') }}" class="shortcut-link" style="background:#faf5ff; border-color:#ddd6fe;">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl text-xl text-white" style="background:#7c3aed;">📋</div>
            <div>
                <p class="font-bold text-violet-900 text-sm">KSA Luas Panen</p>
                <p class="text-xs text-violet-400 mt-0.5">Sanding bulanan per tahun</p>
            </div>
            <svg class="ml-auto h-5 w-5 text-violet-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
        <a href="{{ route('user.ksa.produksi.bulanan.index') }}" class="shortcut-link" style="background:#eff6ff; border-color:#bfdbfe;">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl text-xl text-white" style="background:#2563eb;">🏭</div>
            <div>
                <p class="font-bold text-blue-900 text-sm">KSA Produksi</p>
                <p class="text-xs text-blue-400 mt-0.5">Sanding bulanan per tahun</p>
            </div>
            <svg class="ml-auto h-5 w-5 text-blue-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    </div>

</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Highcharts.chart('inlineChartContainer', {
        chart: { 
            type: 'spline', 
            style: { fontFamily: 'inherit' },
            backgroundColor: 'transparent'
        },
        title: { text: null },
        xAxis: { 
            categories: @json($chartBulan),
            gridLineWidth: 0,
            lineColor: '#e2e8f0',
            tickColor: '#e2e8f0'
        },
        yAxis: { 
            title: { text: 'Luas (Hektar)' },
            gridLineColor: '#f1f5f9',
            gridLineDashStyle: 'longdash'
        },
        tooltip: { shared: true, valueSuffix: ' Ha', backgroundColor: 'rgba(255, 255, 255, 0.95)', borderRadius: 10, borderWidth: 0, shadow: true },
        credits: { enabled: false },
        plotOptions: {
            spline: { 
                marker: { radius: 5, enabled: true, symbol: 'circle' },
                lineWidth: 3
            }
        },
        series: [{
            name: 'Luas Tanam',
            data: @json($chartTanam),
            color: '#16a34a'
        }, {
            name: 'Luas Panen',
            data: @json($chartPanen),
            color: '#d97706'
        }]
    });
});
</script>
@endsection