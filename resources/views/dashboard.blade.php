@extends('layouts.app')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
.dash-root { font-family: 'Plus Jakarta Sans', sans-serif; }

.hero-admin {
    background: linear-gradient(135deg, #052e16 0%, #14532d 30%, #166534 60%, #15803d 100%);
    position: relative; overflow: hidden; border-radius: 20px;
}
.hero-admin::before {
    content: ''; position: absolute; inset: 0;
    background:
        radial-gradient(circle at 15% 60%, rgba(134,239,172,.14) 0%, transparent 45%),
        radial-gradient(circle at 85% 15%, rgba(74,222,128,.10) 0%, transparent 40%);
}
.hero-circle  { position:absolute; right:-80px; bottom:-80px; width:280px; height:280px; border-radius:50%; border:50px solid rgba(255,255,255,.04); pointer-events:none; }
.hero-circle2 { position:absolute; right:60px; top:-100px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.03); pointer-events:none; }

.stat-card {
    border-radius: 18px; overflow: hidden; position: relative;
    transition: transform .2s ease, box-shadow .2s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.18); }
.stat-card-inner { padding: 22px 22px 18px; }

.section-card {
    background: #fff; border-radius: 18px;
    border: 1.5px solid #f1f5f9;
    box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.04);
    overflow: hidden;
}
.section-header {
    padding: 16px 22px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    background: #fafafa;
}

.recent-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 20px; border-bottom: 1px solid #f8fafc; transition: background .15s;
}
.recent-item:last-child { border-bottom: none; }
.recent-item:hover { background: #f9fafb; }

.qaction {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 18px 12px; border-radius: 16px; border: 1.5px solid #f1f5f9;
    background: #fff; transition: all .2s; text-decoration: none;
}
.qaction:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
.qaction-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px; font-size: 20px; transition: transform .2s;
}
.qaction:hover .qaction-icon { transform: scale(1.1); }

.grafik-item {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px; border-radius: 14px; border: 1.5px solid #f1f5f9;
    transition: all .2s; text-decoration: none;
}
.grafik-item:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.05); border-color: #bbf7d0; background:#f0fdf4; }

.shortcut-link {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 18px; border-radius: 16px; border: 1.5px solid;
    text-decoration: none; transition: all .2s;
}
.shortcut-link:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
</style>

<div class="dash-root space-y-5 pb-6">

    {{-- HERO --}}
    <div class="hero-admin px-7 py-9 text-white">
        <div class="hero-circle"></div>
        <div class="hero-circle2"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl text-3xl"
                     style="background:rgba(255,255,255,.12); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2);">🌾</div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="mt-1 text-sm text-green-200 font-medium">Rekap Padi Sumatera Selatan &nbsp;·&nbsp; {{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>
            <div class="hidden lg:block flex-shrink-0">
                <div class="rounded-xl px-6 py-3" style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);">
                    <p class="text-xs font-semibold text-green-200 uppercase tracking-wide">Hak Akses</p>
                    <p class="mt-1 text-lg font-bold text-white">{{ Auth::user()->role->display_name }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
            <div class="stat-card-inner flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide">Kabupaten</p>
                    <p class="mt-2 text-4xl font-bold text-white">{{ $totalKabupaten }}</p>
                    <p class="mt-1 text-xs text-blue-200">Wilayah terdaftar</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background:rgba(255,255,255,.15);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#22c55e,#15803d);">
            <div class="stat-card-inner flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-100 uppercase tracking-wide">Luas Tanam</p>
                    <p class="mt-2 text-4xl font-bold text-white">{{ number_format($totalRekapTanam, 0, ',', '.') }}<span class="text-lg ml-1 font-medium">Ha</span></p>
                    <p class="mt-1 text-xs text-green-200">Tahun {{ date('Y') }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background:rgba(255,255,255,.15);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <div class="stat-card-inner flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-100 uppercase tracking-wide">Luas Panen</p>
                    <p class="mt-2 text-4xl font-bold text-white">{{ number_format($totalRekapPanen, 0, ',', '.') }}<span class="text-lg ml-1 font-medium">Ha</span></p>
                    <p class="mt-1 text-xs text-yellow-200">Tahun {{ date('Y') }}</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background:rgba(255,255,255,.15);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
            </div>
        </div>
        @if(Auth::user()->canManageUsers())
        <div class="stat-card" style="background:linear-gradient(135deg,#a855f7,#7c3aed);">
            <div class="stat-card-inner flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-100 uppercase tracking-wide">Pengguna</p>
                    <p class="mt-2 text-4xl font-bold text-white">{{ $totalUsers }}</p>
                    <p class="mt-1 text-xs text-purple-200">Akun aktif</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background:rgba(255,255,255,.15);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- AKSI CEPAT --}}
    @if(Auth::user()->canManageData())
    <div class="section-card">
        <div class="section-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Aksi Cepat</h3>
                <p class="text-xs text-gray-400 mt-0.5">Input data langsung dari sini</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
                <a href="{{ route('rekap.harian.tanam.create') }}" class="qaction" style="border-color:#bbf7d0; background:linear-gradient(135deg,#f0fdf4,#fff);">
                    <div class="qaction-icon" style="background:#dcfce7;">🌱</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Tambah Rekap Tanam</h4>
                </a>
                <a href="{{ route('rekap.harian.panen.create') }}" class="qaction" style="border-color:#fde68a; background:linear-gradient(135deg,#fffbeb,#fff);">
                    <div class="qaction-icon" style="background:#fef3c7;">🌾</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Tambah Rekap Panen</h4>
                </a>
                <a href="{{ route('ksa.tanam.bulanan.create') }}" class="qaction" style="border-color:#fed7aa; background:linear-gradient(135deg,#fff7ed,#fff);">
                    <div class="qaction-icon" style="background:#ffedd5;">📊</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Input KSA Tanam</h4>
                </a>
                <a href="{{ route('ksa.panen.bulanan.create') }}" class="qaction" style="border-color:#ddd6fe; background:linear-gradient(135deg,#faf5ff,#fff);">
                    <div class="qaction-icon" style="background:#ede9fe;">📋</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Input KSA Panen</h4>
                </a>
                <a href="{{ route('ksa.produksi.bulanan.create') }}" class="qaction" style="border-color:#bfdbfe; background:linear-gradient(135deg,#eff6ff,#fff);">
                    <div class="qaction-icon" style="background:#dbeafe;">🏭</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Input KSA Produksi</h4>
                </a>
                <a href="{{ route('lbs.create') }}" class="qaction" style="border-color:#99f6e4; background:linear-gradient(135deg,#f0fdfa,#fff);">
                    <div class="qaction-icon" style="background:#ccfbf1;">🗾</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Input LBS</h4>
                </a>
                <a href="{{ route('admin.kabupaten.index') }}" class="qaction" style="border-color:#e2e8f0; background:linear-gradient(135deg,#f8fafc,#fff);">
                    <div class="qaction-icon" style="background:#f1f5f9;">🗺️</div>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight">Kelola Wilayah</h4>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- GRAFIK INTERAKTIF INLINE --}}
    <div class="section-card">
        <div class="section-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Perkembangan Luas Tanam &amp; Panen {{ date('Y') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Visibilitas data pergerakan tren komoditas secara *real-time* (Hektar)</p>
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
                        <p class="text-xs text-gray-400">5 data terkini</p>
                    </div>
                </div>
                <a href="{{ route('rekap.bulanan.tanam.index') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white" style="background:#16a34a;">
                    Lihat Semua
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            <div>
                @if($recentTanam->count() > 0)
                    @foreach($recentTanam as $i => $tanam)
                    <div class="recent-item">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-xs font-bold" style="background:#f0fdf4; color:#16a34a;">#{{ $i+1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $tanam->kabupaten->nama_kabupaten }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $tanam->tahun }} &nbsp;·&nbsp; <span class="font-semibold text-green-600">{{ number_format($tanam->total, 2, ',', '.') }} Ha</span></p>
                            </div>
                        </div>
                        <a href="{{ route('rekap.bulanan.tanam.show', $tanam) }}" class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg text-white" style="background:#16a34a;">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                    @endforeach
                @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <span class="text-3xl mb-2">🌱</span>
                    <p class="text-sm font-medium text-gray-400">Belum ada data tanam</p>
                </div>
                @endif
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
                        <p class="text-xs text-gray-400">5 data terkini</p>
                    </div>
                </div>
                <a href="{{ route('rekap.bulanan.panen.index') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white" style="background:#d97706;">
                    Lihat Semua
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            <div>
                @if($recentPanen->count() > 0)
                    @foreach($recentPanen as $i => $panen)
                    <div class="recent-item">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-xs font-bold" style="background:#fffbeb; color:#b45309;">#{{ $i+1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $panen->kabupaten->nama_kabupaten }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $panen->tahun }} &nbsp;·&nbsp; <span class="font-semibold text-yellow-600">{{ number_format($panen->total, 2, ',', '.') }} Ha</span></p>
                            </div>
                        </div>
                        <a href="{{ route('rekap.bulanan.panen.show', $panen) }}" class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg text-white" style="background:#d97706;">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                    @endforeach
                @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <span class="text-3xl mb-2">🌾</span>
                    <p class="text-sm font-medium text-gray-400">Belum ada data panen</p>
                </div>
                @endif
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
                    ['label'=>'Grafik Luas Tanam',     'sub'=>'Perbandingan LTT tahunan',   'route'=>'grafik.ltt.tanam',    'emoji'=>'📈'],
                    ['label'=>'Grafik Luas Panen',     'sub'=>'Perbandingan LTP tahunan',   'route'=>'grafik.ltt.panen',    'emoji'=>'📊'],
                    ['label'=>'Grafik KSA Luas Tanam', 'sub'=>'Sanding KSA per tahun',      'route'=>'grafik.ksa.tanam',    'emoji'=>'🌱'],
                    ['label'=>'Grafik KSA Luas Panen', 'sub'=>'Sanding KSA per tahun',      'route'=>'grafik.ksa.panen',    'emoji'=>'🌾'],
                    ['label'=>'Grafik KSA Produksi',   'sub'=>'Produksi padi per tahun',    'route'=>'grafik.ksa.produksi', 'emoji'=>'🏭'],
                    ['label'=>'Grafik IP Padi',        'sub'=>'Indeks pertanaman padi',     'route'=>'grafik.ip.padi',      'emoji'=>'🌿'],
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
        <a href="{{ route('ksa.tanam.index') }}" class="shortcut-link" style="background:#fff7ed; border-color:#fed7aa;">
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-lg text-white" style="background:#ea580c;">📊</div>
            <div>
                <p class="font-bold text-sm" style="color:#7c2d12;">KSA Luas Tanam</p>
                <p class="text-xs mt-0.5" style="color:#fb923c;">Sanding bulanan per tahun</p>
            </div>
            <svg class="ml-auto h-4 w-4 flex-shrink-0" style="color:#fdba74;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
        <a href="{{ route('ksa.panen.index') }}" class="shortcut-link" style="background:#faf5ff; border-color:#ddd6fe;">
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-lg text-white" style="background:#7c3aed;">📋</div>
            <div>
                <p class="font-bold text-violet-900 text-sm">KSA Luas Panen</p>
                <p class="text-xs text-violet-400 mt-0.5">Sanding bulanan per tahun</p>
            </div>
            <svg class="ml-auto h-4 w-4 text-violet-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
        <a href="{{ route('ksa.produksi.index') }}" class="shortcut-link" style="background:#eff6ff; border-color:#bfdbfe;">
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-lg text-white" style="background:#2563eb;">🏭</div>
            <div>
                <p class="font-bold text-blue-900 text-sm">KSA Produksi</p>
                <p class="text-xs text-blue-400 mt-0.5">Sanding produksi tahunan</p>
            </div>
            <svg class="ml-auto h-4 w-4 text-blue-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    {{-- SHORTCUT — LBS · IP PADI --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('lbs.index') }}" class="shortcut-link" style="background:#f0fdfa; border-color:#99f6e4;">
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-lg text-white" style="background:#0d9488;">🗾</div>
            <div>
                <p class="font-bold text-teal-900 text-sm">Luas Baku Sawah</p>
                <p class="text-xs text-teal-400 mt-0.5">Sanding data LBS tahunan</p>
            </div>
            <svg class="ml-auto h-4 w-4 text-teal-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
        <a href="{{ route('ip.padi.index') }}" class="shortcut-link" style="background:#f0fdf4; border-color:#bbf7d0;">
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-lg text-white" style="background:#16a34a;">🌿</div>
            <div>
                <p class="font-bold text-green-900 text-sm">IP Padi</p>
                <p class="text-xs text-green-400 mt-0.5">Indeks pertanaman tahunan</p>
            </div>
            <svg class="ml-auto h-4 w-4 text-green-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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