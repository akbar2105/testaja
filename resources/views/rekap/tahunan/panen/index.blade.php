@extends('layouts.app')

@section('title', 'Rekap Tahunan Luas Panen')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/>
                    </svg>
                </div>
                Rekap Tahunan Luas Panen
            </h1>
            <p class="mt-1 text-sm text-gray-600">Sanding Total LTT Padi Januari–Desember per Kabupaten/Kota di Sumatera Selatan</p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
            <a href="{{ route('rekap.tahunan.panen.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- FILTER --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Awal</label>
                    <select name="tahun_awal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahunAwal ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Akhir</label>
                    <select name="tahun_akhir" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahunAkhir ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota</label>
                    <select name="kabupaten_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        <option value="">Semua Kabupaten/Kota</option>
                        @foreach($semuaKabupaten as $kab)
                            <option value="{{ $kab->id }}" {{ (isset($kabupatenId) && $kabupatenId == $kab->id) ? 'selected' : '' }}>
                                {{ $kab->nama_kabupaten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('rekap.tahunan.panen.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-all">Reset</a>
                </div>
            </div>
        </div>
    </form>

    {{-- INFO --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm text-green-800 font-medium">
                <strong>Informasi:</strong> Data rekap tahunan dihitung dari total luas panen Januari s.d. Desember per kabupaten &bull;
                Menampilkan <strong>{{ $tahunDipilih->count() }}</strong> tahun ({{ $tahunDipilih->first() }}–{{ $tahunDipilih->last() }})
            </p>
        </div>
    </div>

    {{-- TABLE — lebar kolom seragam dengan rekap bulanan & harian --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    {{-- Header Row 1: identik dengan bulanan (py-3, font-bold) --}}
                    <tr class="bg-gradient-to-r from-green-200 to-green-300 text-gray-800 text-center font-bold">
                        <th class="border border-gray-300 px-3 py-3" rowspan="2">No</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold" colspan="{{ $tahunDipilih->count() }}">Total LTP Januari – Desember</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold" rowspan="2">Total</th>
                    </tr>
                    {{-- Header Row 2: min-w-[64px] seragam dengan kolom bulan di rekap bulanan --}}
                    <tr class="bg-gradient-to-r from-green-100 to-green-200 text-gray-700 font-semibold">
                        @foreach($tahunDipilih as $tahun)
                            <th class="border border-gray-300 px-2 py-2 text-center min-w-[64px]">{{ $tahun }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($formattedData as $index => $row)
                        <tr class="hover:bg-green-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-600">{{ $index + 1 }}</td>
                            <td class="border border-gray-200 px-4 py-2.5 font-bold text-gray-900 whitespace-nowrap">{{ $row['nama_kabupaten'] }}</td>
                            @foreach($tahunDipilih as $tahun)
                                @php $total = $row['years'][$tahun]; @endphp
                                <td class="border border-gray-200 px-3 py-2.5 text-right font-medium {{ $total > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $total == 0 ? '0' : (floor($total) == $total ? number_format($total, 0, ',', '.') : number_format($total, 2, ',', '.')) }}
                                </td>
                            @endforeach
                            @php $rowTotal = $row['row_total']; @endphp
                            <td class="border border-gray-200 px-4 py-2.5 text-right font-bold text-gray-900">
                                {{ $rowTotal == 0 ? '0' : (floor($rowTotal) == $rowTotal ? number_format($rowTotal, 0, ',', '.') : number_format($rowTotal, 2, ',', '.')) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tahunDipilih->count() + 3 }}" class="border border-gray-200 text-center py-12">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-lg font-semibold">Data luas panen belum tersedia</p>
                                    <p class="text-sm mt-2">untuk rentang tahun {{ $tahunAwal }}–{{ $tahunAkhir }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($kabupatens->count() > 0)
                {{-- Footer: py-3, identik dengan bulanan --}}
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold">
                        <td class="border border-green-400 px-3 py-3 text-center" colspan="2">
                            <span class="text-xs uppercase tracking-wide font-extrabold">SUMATERA SELATAN</span>
                        </td>
                        @foreach($tahunDipilih as $tahun)
                            @php $tPT = $totalPerTahun->get($tahun, 0); @endphp
                            <td class="border border-green-400 px-3 py-3 text-right">{{ $tPT == 0 ? '0' : (floor($tPT) == $tPT ? number_format($tPT, 0, ',', '.') : number_format($tPT, 2, ',', '.')) }}</td>
                        @endforeach
                        <td class="border border-green-400 px-4 py-3 text-right text-white font-extrabold text-sm">
                            {{ $sumTotal == 0 ? '0' : (floor($sumTotal) == $sumTotal ? number_format($sumTotal, 0, ',', '.') : number_format($sumTotal, 2, ',', '.')) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <p class="mt-3 text-xs text-gray-400">* Satuan: Hektar (Ha) &nbsp;|&nbsp; Periode: Januari – Desember</p>

    @if($kabupatens->count() > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Kabupaten</p><p class="text-3xl font-bold mt-2">{{ $kabupatens->count() }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Rentang Tahun</p><p class="text-3xl font-bold mt-2">{{ $tahunDipilih->count() }}</p><p class="text-xs mt-1 opacity-75">{{ $tahunAwal }} – {{ $tahunAkhir }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Luas Panen Tertinggi</p>
                    <p class="text-3xl font-bold mt-2">{{ $maxTotal == 0 ? '0' : (floor($maxTotal) == $maxTotal ? number_format($maxTotal, 0, ',', '.') : number_format($maxTotal, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Hektar</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-lime-500 to-lime-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Semua Tahun</p><p class="text-3xl font-bold mt-2">{{ $sumTotal == 0 ? '0' : (floor($sumTotal) == $sumTotal ? number_format($sumTotal, 0, ',', '.') : number_format($sumTotal, 2, ',', '.')) }}</p><p class="text-xs mt-1 opacity-75">Hektar</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg></div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.overflow-x-auto::-webkit-scrollbar { height: 8px }
.overflow-x-auto::-webkit-scrollbar-track { background: #f0fdf4; border-radius: 4px }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #86efac; border-radius: 4px }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #16a34a }
</style>
@endsection