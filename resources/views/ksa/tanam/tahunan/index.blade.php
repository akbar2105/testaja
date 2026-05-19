@extends('layouts.app')

@section('title', 'KSA Sanding Tahunan per Bulan Luas Tanam')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- ── HEADER ── --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                KSA LTT Padi Sanding Tahunan per Bulan
            </h1>
            <p class="mt-2 text-sm text-gray-600">KSA &mdash; Perbandingan antar tahun untuk bulan yang sama &bull; Satuan: Hektar (Ha)</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('ksa.tanam.export', ['bulan' => $bulan, 'tahun_awal' => $tahunAwal, 'tahun_akhir' => $tahunAkhir]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ── FILTER ── --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Bulan</label>
                    <select name="bulan" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                        @foreach($bulanList as $b => $nama)
                            <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tahun Awal</label>
                    <select name="tahun_awal" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $tahunAwal ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tahun Akhir</label>
                    <select name="tahun_akhir" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $tahunAkhir ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kabupaten/Kota</label>
                    <select name="kabupaten_id" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                        <option value="">— Semua Kabupaten —</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ $kab->id == $kabupatenId ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">&nbsp;</label>
                    <div class="flex gap-2 h-10">
                        <button type="submit" class="flex-1 h-10 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 font-semibold shadow-md transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Tampilkan
                        </button>
                        @if($kabupatenId || $bulan != 10 || $tahunAwal != min($availableYears) || $tahunAkhir != max($availableYears))
                        <a href="{{ route('ksa.tanam.index') }}" class="h-10 px-4 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-all whitespace-nowrap">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- ── INFO ── --}}
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-orange-800 font-medium">
                <strong>Sanding Tahunan per Bulan &mdash; {{ $namaBulan }}</strong> &mdash; Membandingkan LTT per kabupaten di bulan yang sama lintas tahun.
                Satuan: <strong>Hektar (Ha)</strong>
            </p>
        </div>
    </div>

    {{-- ── TABEL ── --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-orange-500 to-orange-600 text-white">
                        <th class="border border-orange-600 px-3 py-3 text-center font-bold" rowspan="2">No</th>
                        <th class="border border-orange-600 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-orange-600 px-4 py-2 text-center font-bold" colspan="{{ $tahunAkhir - $tahunAwal + 1 }}">LTT Padi &mdash; {{ $namaBulan }}</th>
                        <th class="border border-orange-600 px-3 py-3 text-center font-bold min-w-[100px]" rowspan="2">Total</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-orange-400 to-orange-500 text-white text-xs">
                        @for($y = $tahunAwal; $y <= $tahunAkhir; $y++)
                        <th class="border border-orange-500 px-3 py-2 text-center font-bold min-w-[80px]">{{ $y }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($groupedData as $i => $row)
                    <tr class="hover:bg-orange-50 transition-colors duration-150 {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-600">{{ $i + 1 }}</td>
                        {{-- ✅ FIX: $row['kabupaten'] adalah Eloquent object → harus pakai ->nama_kabupaten --}}
                        <td class="border border-gray-200 px-4 py-2.5 font-bold text-gray-900 whitespace-nowrap">{{ $row['kabupaten']->nama_kabupaten }}</td>
                        @for($y = $tahunAwal; $y <= $tahunAkhir; $y++)
                        @php $val = $row['years'][$y] ?? null; @endphp
                        <td class="border border-gray-200 px-3 py-2.5 text-right {{ $val > 0 ? 'text-gray-900 font-medium' : 'text-gray-300' }}">
                            {{ ($val ?? 0) == 0 ? '0' : (floor($val) == $val ? number_format($val, 0, ',', '.') : number_format($val, 2, ',', '.')) }}
                        </td>
                        @endfor
                        {{-- Total per kabupaten --}}
                        @php $rowTotal = $row['total'] ?? 0; @endphp
                        <td class="border border-orange-200 px-3 py-2.5 text-right font-bold {{ $rowTotal > 0 ? 'text-orange-700 bg-orange-50' : 'text-gray-300' }}">
                            {{ ($rowTotal ?? 0) == 0 ? '0' : (floor($rowTotal) == $rowTotal ? number_format($rowTotal, 0, ',', '.') : number_format($rowTotal, 2, ',', '.')) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $tahunAkhir - $tahunAwal + 4 }}" class="py-10 text-center text-gray-400 font-semibold">Belum ada data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-orange-700 to-orange-800 text-white font-bold">
                        <td colspan="2" class="border border-orange-800 px-4 py-3 text-center font-extrabold tracking-wide uppercase">SUMATERA SELATAN</td>
                        @for($y = $tahunAwal; $y <= $tahunAkhir; $y++)
                        @php $tot = $totals[$y] ?? 0; @endphp
                        <td class="border border-orange-800 px-3 py-3 text-right font-extrabold">
                            {{ ($tot ?? 0) == 0 ? '0' : (floor($tot) == $tot ? number_format($tot, 0, ',', '.') : number_format($tot, 2, ',', '.')) }}
                        </td>
                        @endfor
                        {{-- Grand total --}}
                        <td class="border border-orange-800 px-3 py-3 text-right font-extrabold bg-orange-900">
                            {{ ($grandTotal ?? 0) == 0 ? '0' : (floor($grandTotal) == $grandTotal ? number_format($grandTotal, 0, ',', '.') : number_format($grandTotal, 2, ',', '.')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <p class="mt-3 text-xs text-gray-400">* Satuan: Hektar (Ha) &nbsp;|&nbsp; Bulan: {{ $namaBulan }} &nbsp;|&nbsp; Rentang: {{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>

    {{-- ── SUMMARY CARDS ── --}}
    @if(count($groupedData) > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Kabupaten</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalKabupaten }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">LTT Tertinggi {{ $tahunAkhir }}</p>
                    <p class="text-3xl font-bold mt-2">{{ ($maxVal ?? 0) == 0 ? '0' : (floor($maxVal) == $maxVal ? number_format($maxVal, 0, ',', '.') : number_format($maxVal, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Hektar</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-600 to-red-500 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total {{ $namaBulan }} {{ $tahunAkhir }}</p>
                    <p class="text-3xl font-bold mt-2">{{ ($totAkhir ?? 0) == 0 ? '0' : (floor($totAkhir) == $totAkhir ? number_format($totAkhir, 0, ',', '.') : number_format($totAkhir, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Hektar</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rentang Tahun</p>
                    <p class="text-3xl font-bold mt-2">{{ $rentang }}</p>
                    <p class="text-xs mt-1 opacity-75">{{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
<style>
.overflow-x-auto::-webkit-scrollbar { height: 8px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #fff7ed; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #f97316; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #ea580c; }
</style>
@endsection