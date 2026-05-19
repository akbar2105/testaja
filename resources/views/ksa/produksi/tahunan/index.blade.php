@extends('layouts.app')

@section('title', 'KSA Produksi Padi Sanding Tahunan Bulan ' . $namaBulan)

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- ── HEADER ── --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                Produksi Padi Sanding KSA {{ $tahunAwal }}&ndash;{{ $tahunAkhir }} Bulan {{ $namaBulan }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">KSA &mdash; Data otomatis dari Sanding Bulanan &mdash; Satuan: Ton GKG</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('user.ksa.produksi.export', ['bulan' => $bulan, 'tahun_awal' => $tahunAwal, 'tahun_akhir' => $tahunAkhir]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ── FILTER ── --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                    <select name="bulan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        @foreach($bulanList as $b => $nama)
                            <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Awal</label>
                    <select name="tahun_awal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $tahunAwal ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Akhir</label>
                    <select name="tahun_akhir" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $tahunAkhir ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota</label>
                    <select name="kabupaten_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        <option value="">— Semua Kabupaten —</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ $kab->id == $kabupatenId ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 font-semibold shadow-md transition-all">
                        Tampilkan
                    </button>
                    @if($kabupatenId || $bulan != 1 || $tahunAwal != min($availableYears) || $tahunAkhir != max($availableYears))
                    <a href="{{ route('user.ksa.produksi.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-all whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ── INFO ALERT ── --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-blue-800 font-medium">
                <strong>Sanding Tahunan per Bulan</strong> &mdash; Data <strong>otomatis</strong> dari
                <a href="{{ route('user.ksa.produksi.bulanan.index') }}" class="underline font-bold">Sanding Bulanan per Tahun</a>.
                Bulan: <strong>{{ $namaBulan }}</strong> &bull; Periode: {{ $tahunAwal }}&ndash;{{ $tahunAkhir }} &bull; Satuan: Ton GKG
            </p>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <th class="border border-blue-700 px-3 py-3 text-center font-bold" rowspan="2">No</th>
                        <th class="border border-blue-700 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-blue-700 px-4 py-3 text-center font-bold" colspan="{{ count($years) }}">Produksi Padi (Ton GKG) &mdash; {{ $namaBulan }}</th>
                        <th class="border border-blue-700 px-4 py-3 text-center font-bold min-w-[100px]" rowspan="2">Total</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white">
                        @foreach($years as $year)
                            <th class="border border-blue-600 px-3 py-2 text-center font-bold min-w-[90px]">{{ $year }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($groupedData as $index => $data)
                    <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-600">{{ $index + 1 }}</td>
                        <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900 whitespace-nowrap">{{ $data['kabupaten']->nama_kabupaten }}</td>
                        @foreach($years as $year)
                            @php $val = $data['years'][$year] ?? 0; @endphp
                            <td class="border border-gray-200 px-3 py-2.5 text-right font-medium {{ $val > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                                {{ ($val ?? 0) == 0 ? '0' : (floor($val) == $val ? number_format($val, 0, ',', '.') : number_format($val, 2, ',', '.')) }}
                            </td>
                        @endforeach
                        <td class="border border-blue-200 px-3 py-2.5 text-right font-bold {{ $data['total'] > 0 ? 'text-blue-700 bg-blue-50' : 'text-gray-300' }}">
                            {{ ($data['total'] ?? 0) == 0 ? '0' : (floor($data['total'] ?? 0) == ($data['total'] ?? 0) ? number_format($data['total'] ?? 0, 0, ',', '.') : number_format($data['total'] ?? 0, 2, ',', '.')) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($years) + 3 }}" class="border border-gray-300 text-center py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-lg font-semibold">Data KSA belum tersedia</p>
                                <p class="text-sm mt-2">untuk bulan <strong>{{ $namaBulan }}</strong> periode {{ $tahunAwal }}-{{ $tahunAkhir }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($groupedData) > 0)
                <tfoot>
                    <tr class="bg-gradient-to-r from-blue-700 to-indigo-700 text-white font-bold">
                        <td class="border border-blue-800 px-3 py-3 text-center" colspan="2"><span class="text-xs uppercase tracking-wide font-extrabold">SUMATERA SELATAN</span></td>
                        @foreach($years as $year)
                            @php $tot = $totals[$year] ?? 0; @endphp
                            <td class="border border-blue-800 px-3 py-3 text-right font-extrabold">
                                {{ ($tot ?? 0) == 0 ? '0' : (floor($tot) == $tot ? number_format($tot, 0, ',', '.') : number_format($tot, 2, ',', '.')) }}
                            </td>
                        @endforeach
                        <td class="border border-blue-800 px-3 py-3 text-right font-extrabold bg-blue-900">
                            {{ ($grandTotal ?? 0) == 0 ? '0' : (floor($grandTotal) == $grandTotal ? number_format($grandTotal, 0, ',', '.') : number_format($grandTotal, 2, ',', '.')) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <p class="mt-3 text-xs text-gray-400">* Satuan: Hektar (Ha) &nbsp;|&nbsp; Bulan: {{ $namaBulan }} &nbsp;|&nbsp; Rentang: {{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>

    {{-- ── SUMMARY CARDS ── --}}
    @if(count($groupedData) > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Card 1: Total Kabupaten --}}
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Kabupaten</p><p class="text-3xl font-bold mt-2">{{ $totalKabupaten }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            </div>
        </div>

        {{-- Card 2: Produksi Tertinggi tahun akhir --}}
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Produksi Tertinggi {{ $tahunAkhir }}</p>
                    <p class="text-3xl font-bold mt-2">{{ ($maxVal ?? 0) == 0 ? '0' : (floor($maxVal) == $maxVal ? number_format($maxVal, 0, ',', '.') : number_format($maxVal, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Ton GKG</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
        </div>

        {{-- Card 3: Total bulan ini tahun akhir --}}
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total {{ $namaBulan }} {{ $tahunAkhir }}</p>
                    <p class="text-3xl font-bold mt-2">{{ ($totAkhir ?? 0) == 0 ? '0' : (floor($totAkhir ?? 0) == ($totAkhir ?? 0) ? number_format($totAkhir ?? 0, 0, ',', '.') : number_format($totAkhir ?? 0, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Ton GKG</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
        </div>

        {{-- Card 4: Rentang Tahun --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rentang Tahun</p>
                    <p class="text-3xl font-bold mt-2">{{ $rentang }}</p>
                    <p class="text-xs mt-1 opacity-75">{{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            </div>
        </div>

    </div>
    @endif

</div>
<style>
.overflow-x-auto::-webkit-scrollbar { height: 8px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #eff6ff; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #2563eb; }
</style>
@endsection
