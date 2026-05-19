<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Rekap Padi Sumsel') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Figtree','ui-sans-serif','system-ui','sans-serif'] },
                    colors: { primary: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d' } }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Figtree', sans-serif; }

        /* NAV ITEMS */
        .nav-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            background: transparent;
            border: none;
            line-height: 1.25;
            transition: background 0.15s, color 0.15s;
            letter-spacing: 0.01em;
        }
        .nav-item:hover { background: #f0fdf4; color: #16a34a; }
        .nav-item.active { background: #dcfce7; color: #15803d; }
        .nav-item svg.nav-icon { flex-shrink: 0; }

        /* DROPDOWN PANEL */
        .dd-panel {
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            min-width: 220px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
            padding: 6px;
            z-index: 200;
        }
        .dd-section {
            padding: 8px 10px 3px;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9ca3af;
        }
        .dd-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            transition: background 0.12s, color 0.12s;
        }
        .dd-item:hover { background: #f0fdf4; color: #16a34a; }
        .dd-item.active { color: #16a34a; font-weight: 600; background: #f0fdf4; }
        .dd-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 5px;
            font-size: 0.6rem;
            font-weight: 700;
            background: #f3f4f6;
            color: #6b7280;
            flex-shrink: 0;
        }
        .dd-badge.active { background: #dcfce7; color: #15803d; }
        .dd-divider { height: 1px; background: #f3f4f6; margin: 4px 2px; }
        .chevron { transition: transform 0.2s ease; display: inline-block; }
        .chevron.open { transform: rotate(90deg); }

        /* MOBILE ITEMS */
        .mob-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            transition: background 0.12s;
        }
        .mob-item:hover { background: #f9fafb; color: #16a34a; }
        .mob-item.active { background: #f0fdf4; color: #16a34a; font-weight: 600; }
        .mob-section {
            padding: 10px 10px 3px;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        /* HEADER 3-COLUMN GRID */
        .header-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            height: 60px;
            padding: 0 20px;
            gap: 8px;
        }
        .header-left  { display: flex; align-items: center; gap: 10px; }
        .header-center { display: flex; align-items: center; justify-content: center; gap: 2px; }
        .header-right { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">

@php
    $lttActive    = request()->routeIs('user.rekap.bulanan.tanam.*') || request()->routeIs('user.rekap.tahunan.tanam.*') || request()->routeIs('user.rekap.harian.tanam.*')
                 || request()->routeIs('user.rekap.bulanan.panen.*') || request()->routeIs('user.rekap.tahunan.panen.*') || request()->routeIs('user.rekap.harian.panen.*');
    $tanamActive  = request()->routeIs('user.rekap.bulanan.tanam.*') || request()->routeIs('user.rekap.tahunan.tanam.*') || request()->routeIs('user.rekap.harian.tanam.*');
    $panenActive  = request()->routeIs('user.rekap.bulanan.panen.*') || request()->routeIs('user.rekap.tahunan.panen.*') || request()->routeIs('user.rekap.harian.panen.*');
    $ksaActive    = request()->routeIs('user.ksa.*');
    $ipActive     = request()->routeIs('user.ip.padi.*');
    $lbsActive    = request()->routeIs('user.lbs.*');
    $grafikActive = request()->routeIs('user.grafik.*');
@endphp

<header x-data="{ mob: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">

    {{-- DESKTOP HEADER --}}
    <div class="hidden lg:grid header-grid">

        {{-- LEFT: Logo --}}
        <div class="header-left">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-base shadow-sm" style="background:linear-gradient(135deg,#16a34a,#14532d)">🌾</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 leading-tight">Rekap Padi Sumsel</p>
                    <p class="text-xs text-gray-400 leading-tight">Sumatera Selatan</p>
                </div>
            </a>
        </div>

        {{-- CENTER: Navigation --}}
        <nav class="header-center">

            <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Dashboard
            </a>

            {{-- LTT dan LTP --}}
            <div x-data="{ open: false }" class="relative" @click.outside="open=false">
                <button @click="open=!open" class="nav-item {{ $lttActive ? 'active' : '' }}">
                    <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                    LTT &amp; LTP
                    <svg :class="open ? 'chevron open' : 'chevron'" class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="dd-panel" style="min-width:240px">
                    <p class="dd-section">Luas Tanam</p>
                    <a href="{{ route('user.rekap.bulanan.tanam.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.bulanan.tanam.*') ? 'active' : '' }}"><span class="dd-badge {{ $tanamActive ? 'active' : '' }}">B</span> Rekap Bulanan</a>
                    <a href="{{ route('user.rekap.tahunan.tanam.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.tahunan.tanam.*') ? 'active' : '' }}"><span class="dd-badge {{ $tanamActive ? 'active' : '' }}">T</span> Rekap Tahunan</a>
                    <a href="{{ route('user.rekap.harian.tanam.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.harian.tanam.*') ? 'active' : '' }}"><span class="dd-badge {{ $tanamActive ? 'active' : '' }}">H</span> Rekap Harian</a>
                    <div class="dd-divider"></div>
                    <p class="dd-section">Luas Panen</p>
                    <a href="{{ route('user.rekap.bulanan.panen.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.bulanan.panen.*') ? 'active' : '' }}"><span class="dd-badge {{ $panenActive ? 'active' : '' }}">B</span> Rekap Bulanan</a>
                    <a href="{{ route('user.rekap.tahunan.panen.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.tahunan.panen.*') ? 'active' : '' }}"><span class="dd-badge {{ $panenActive ? 'active' : '' }}">T</span> Rekap Tahunan</a>
                    <a href="{{ route('user.rekap.harian.panen.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.rekap.harian.panen.*') ? 'active' : '' }}"><span class="dd-badge {{ $panenActive ? 'active' : '' }}">H</span> Rekap Harian</a>
                </div>
            </div>

            {{-- KSA --}}
            <div x-data="{ open: false }" class="relative" @click.outside="open=false">
                <button @click="open=!open" class="nav-item {{ $ksaActive ? 'active' : '' }}">
                    <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    KSA
                    <svg :class="open ? 'chevron open' : 'chevron'" class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="dd-panel" style="min-width:250px">
                    <p class="dd-section">KSA Luas Tanam</p>
                    <a href="{{ route('user.ksa.tanam.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.tanam.index') || request()->routeIs('user.ksa.tanam.show') ? 'active' : '' }}">🌱 Sanding Tahunan per Bulan</a>
                    <a href="{{ route('user.ksa.tanam.bulanan.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.tanam.bulanan.*') ? 'active' : '' }}">🌱 Sanding Bulanan per Tahun</a>
                    <a href="{{ route('user.ksa.tanam.total.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.tanam.total.*') ? 'active' : '' }}">🌱 Sanding Total Tahunan</a>
                    <div class="dd-divider"></div>
                    <p class="dd-section">KSA Luas Panen</p>
                    <a href="{{ route('user.ksa.panen.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.panen.index') ? 'active' : '' }}">🌾 Sanding Tahunan per Bulan</a>
                    <a href="{{ route('user.ksa.panen.bulanan.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.panen.bulanan.*') ? 'active' : '' }}">🌾 Sanding Bulanan per Tahun</a>
                    <a href="{{ route('user.ksa.panen.total.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.panen.total.*') ? 'active' : '' }}">🌾 Sanding Total Tahunan</a>
                    <div class="dd-divider"></div>
                    <p class="dd-section">KSA Produksi</p>
                    <a href="{{ route('user.ksa.produksi.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.produksi.index') || request()->routeIs('user.ksa.produksi.show') ? 'active' : '' }}">📊 Sanding Tahunan per Bulan</a>
                    <a href="{{ route('user.ksa.produksi.bulanan.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.produksi.bulanan.*') ? 'active' : '' }}">📊 Sanding Bulanan per Tahun</a>
                    <a href="{{ route('user.ksa.produksi.total.index') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.ksa.produksi.total.*') ? 'active' : '' }}">📊 Sanding Total Tahunan</a>
                </div>
            </div>

            <a href="{{ route('user.ip.padi.index') }}" class="nav-item {{ $ipActive ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/></svg>
                IP Padi
            </a>

            <a href="{{ route('user.lbs.index') }}" class="nav-item {{ $lbsActive ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c-.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
                LBS
            </a>

            {{-- Grafik --}}
            <div x-data="{ open: false }" class="relative" @click.outside="open=false">
                <button @click="open=!open" class="nav-item {{ $grafikActive ? 'active' : '' }}">
                    <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    Grafik
                    <svg :class="open ? 'chevron open' : 'chevron'" class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="dd-panel" style="min-width:260px">
                    <p class="dd-section">LTT &amp; LTP</p>
                    <a href="{{ route('user.grafik.ltt.tanam') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ltt.tanam') ? 'active' : '' }}">📈 Grafik Luas Tanam</a>
                    <a href="{{ route('user.grafik.ltt.panen') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ltt.panen') ? 'active' : '' }}">📈 Grafik Luas Panen</a>
                    <div class="dd-divider"></div>
                    <p class="dd-section">KSA</p>
                    <a href="{{ route('user.grafik.ksa.tanam') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ksa.tanam') ? 'active' : '' }}">📊 Grafik KSA Luas Tanam</a>
                    <a href="{{ route('user.grafik.ksa.panen') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ksa.panen') ? 'active' : '' }}">📊 Grafik KSA Luas Panen</a>
                    <a href="{{ route('user.grafik.ksa.produksi') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ksa.produksi') ? 'active' : '' }}">📊 Grafik KSA Produksi</a>
                    <div class="dd-divider"></div>
                    <p class="dd-section">IP &amp; LBS</p>
                    <a href="{{ route('user.grafik.ip.padi') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.ip.padi') ? 'active' : '' }}">🌿 Grafik IP Padi</a>
                    <a href="{{ route('user.grafik.lbs') }}" @click="open=false" class="dd-item {{ request()->routeIs('user.grafik.lbs') ? 'active' : '' }}">🗺️ Grafik Sanding LBS</a>
                </div>
            </div>

            <a href="{{ route('map') }}" class="nav-item {{ request()->routeIs('map') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c-.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
                Peta
            </a>

        </nav>

        {{-- RIGHT: Auth --}}
        <div class="header-right">
            @auth
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 leading-tight">{{ auth()->user()->role ?? 'User' }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center hover:bg-primary-700 transition-colors shadow-sm" title="Dashboard Admin">
                    <span class="text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    Masuk
                </a>
            @endauth
        </div>

    </div>

    {{-- MOBILE HEADER --}}
    <div class="lg:hidden flex h-14 items-center px-4 gap-3">
        <button @click="mob = !mob" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path x-show="!mob" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                <path x-show="mob"  stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" x-cloak/>
            </svg>
        </button>
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 flex-1">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm" style="background:linear-gradient(135deg,#16a34a,#14532d)">🌾</div>
            <p class="text-sm font-bold text-gray-900 leading-tight">Rekap Padi Sumsel</p>
        </a>
        <div class="ml-auto">
            @auth
                <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center hover:bg-primary-700 transition-colors">
                    <span class="text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors">
                    Masuk
                </a>
            @endauth
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mob" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden border-t border-gray-200 bg-white shadow-lg">
        <div class="px-4 py-3 space-y-0.5 max-h-[80vh] overflow-y-auto">

            <a href="{{ url('/') }}" class="mob-item {{ request()->is('/') ? 'active' : '' }}">🏠 Dashboard</a>

            <p class="mob-section mt-2">LTT — Luas Tanam</p>
            <a href="{{ route('user.rekap.bulanan.tanam.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.bulanan.tanam.*') ? 'active' : '' }}">📋 Rekap Bulanan</a>
            <a href="{{ route('user.rekap.tahunan.tanam.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.tahunan.tanam.*') ? 'active' : '' }}">📅 Rekap Tahunan</a>
            <a href="{{ route('user.rekap.harian.tanam.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.harian.tanam.*') ? 'active' : '' }}">📆 Rekap Harian</a>

            <p class="mob-section mt-2">LTP — Luas Panen</p>
            <a href="{{ route('user.rekap.bulanan.panen.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.bulanan.panen.*') ? 'active' : '' }}">📋 Rekap Bulanan</a>
            <a href="{{ route('user.rekap.tahunan.panen.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.tahunan.panen.*') ? 'active' : '' }}">📅 Rekap Tahunan</a>
            <a href="{{ route('user.rekap.harian.panen.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.rekap.harian.panen.*') ? 'active' : '' }}">📆 Rekap Harian</a>

            <p class="mob-section mt-2">KSA Luas Tanam</p>
            <a href="{{ route('user.ksa.tanam.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.tanam.index') || request()->routeIs('user.ksa.tanam.show') ? 'active' : '' }}">🌱 Sanding Tahunan per Bulan</a>
            <a href="{{ route('user.ksa.tanam.bulanan.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.tanam.bulanan.*') ? 'active' : '' }}">🌱 Sanding Bulanan per Tahun</a>
            <a href="{{ route('user.ksa.tanam.total.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.tanam.total.*') ? 'active' : '' }}">🌱 Sanding Total Tahunan</a>

            <p class="mob-section mt-2">KSA Luas Panen</p>
            <a href="{{ route('user.ksa.panen.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.panen.index') ? 'active' : '' }}">🌾 Sanding Tahunan per Bulan</a>
            <a href="{{ route('user.ksa.panen.bulanan.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.panen.bulanan.*') ? 'active' : '' }}">🌾 Sanding Bulanan per Tahun</a>
            <a href="{{ route('user.ksa.panen.total.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.panen.total.*') ? 'active' : '' }}">🌾 Sanding Total Tahunan</a>

            <p class="mob-section mt-2">KSA Produksi</p>
            <a href="{{ route('user.ksa.produksi.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.produksi.index') || request()->routeIs('user.ksa.produksi.show') ? 'active' : '' }}">📊 Sanding Tahunan per Bulan</a>
            <a href="{{ route('user.ksa.produksi.bulanan.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.produksi.bulanan.*') ? 'active' : '' }}">📊 Sanding Bulanan per Tahun</a>
            <a href="{{ route('user.ksa.produksi.total.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ksa.produksi.total.*') ? 'active' : '' }}">📊 Sanding Total Tahunan</a>

            <p class="mob-section mt-2">IP &amp; LBS</p>
            <a href="{{ route('user.ip.padi.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.ip.padi.*') ? 'active' : '' }}">🌿 IP Tanaman Padi</a>
            <a href="{{ route('user.lbs.index') }}" class="mob-item pl-5 {{ request()->routeIs('user.lbs.*') ? 'active' : '' }}">🗺️ Sanding Luas Baku Sawah</a>

            <p class="mob-section mt-2">Grafik LTT &amp; LTP</p>
            <a href="{{ route('user.grafik.ltt.tanam') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ltt.tanam') ? 'active' : '' }}">📈 Grafik Luas Tanam</a>
            <a href="{{ route('user.grafik.ltt.panen') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ltt.panen') ? 'active' : '' }}">📈 Grafik Luas Panen</a>

            <p class="mob-section mt-2">Grafik KSA</p>
            <a href="{{ route('user.grafik.ksa.tanam') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ksa.tanam') ? 'active' : '' }}">📊 Grafik KSA Luas Tanam</a>
            <a href="{{ route('user.grafik.ksa.panen') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ksa.panen') ? 'active' : '' }}">📊 Grafik KSA Luas Panen</a>
            <a href="{{ route('user.grafik.ksa.produksi') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ksa.produksi') ? 'active' : '' }}">📊 Grafik KSA Produksi</a>

            <p class="mob-section mt-2">Grafik IP &amp; LBS</p>
            <a href="{{ route('user.grafik.ip.padi') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.ip.padi') ? 'active' : '' }}">🌿 Grafik IP Padi</a>
            <a href="{{ route('user.grafik.lbs') }}" class="mob-item pl-5 {{ request()->routeIs('user.grafik.lbs') ? 'active' : '' }}">🗺️ Grafik Sanding LBS</a>

            <a href="{{ route('map') }}" class="mob-item mt-1 {{ request()->routeIs('map') ? 'active' : '' }}">🗺️ Peta</a>

            <div class="border-t border-gray-100 mt-3 pt-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="mob-item font-semibold text-primary-700">📊 Dashboard Admin</a>
                @else
                    <a href="{{ route('login') }}" class="mob-item font-semibold text-primary-700">🔐 Masuk</a>
                @endauth
            </div>
        </div>
    </div>

</header>

<main class="py-8">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

        @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)" class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 px-4 py-3 border border-green-200">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            <p class="text-sm font-medium text-green-800 flex-1">{{ session('success') }}</p>
            <button @click="show=false" class="text-green-500 hover:text-green-700 text-lg leading-none">✕</button>
        </div>
        @endif

        @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl bg-red-50 px-4 py-3 border border-red-200">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }} kesalahan:</p>
                    <ul class="mt-1 list-disc pl-4 text-sm text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                <button @click="show=false" class="text-red-400 hover:text-red-600 text-lg leading-none">✕</button>
            </div>
        </div>
        @endif

        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>