@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    Indeks Pertanaman (IP) Tanaman Padi
                </h1>
                <p class="mt-2 text-sm text-gray-600">Data Indeks Pertanaman Padi Sumatera Selatan &bull; Auto-Sync Aktif ✓</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('ip.padi.export', array_merge(['tahun_awal' => $tahunAwal, 'tahun_akhir' => $tahunAkhir], request()->only(['kabupaten_id']))) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>
    </div>

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
                        <option value="">Semua Kabupaten</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ $kab->id == $kabupatenId ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg transition-all font-semibold">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Tampilkan Data
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- FLASH --}}
    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-r-lg">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- INFO --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <p class="ml-3 text-sm text-green-800 font-medium">
                <strong>Rumus IP:</strong> Total Luas Tanam (Okt–Sep) &divide; Luas Baku Sawah &bull;
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-200 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Auto-Sync Aktif
                </span> &bull;
                Tahun terpilih: <span class="font-bold">{{ implode(', ', $selectedYears) }}</span>
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-green-500 to-green-600 text-white">
                        <th class="border border-green-700 px-2 py-2.5 text-center font-bold" rowspan="2">No</th>
                        <th class="border border-green-700 px-3 py-2.5 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-green-700 px-3 py-2.5 text-center font-bold" colspan="{{ count($selectedYears) }}">
                            Indeks Pertanaman Padi Tahun {{ count($selectedYears) > 1 ? min($selectedYears) . ' - ' . max($selectedYears) : (count($selectedYears) == 1 ? $selectedYears[0] : '') }}
                        </th>
                        <th class="border border-green-700 px-3 py-2.5 text-center font-bold min-w-[110px]" rowspan="2">Total</th>
                        <th class="border border-green-700 px-2 py-2.5 text-center font-bold whitespace-nowrap" rowspan="2">Aksi</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-green-400 to-green-500 text-white text-xs">
                        @foreach($selectedYears as $year)
                            <th class="border border-green-600 px-2 py-2 text-center font-bold min-w-[80px]">{{ $year }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($groupedData as $index => $data)
                    <tr class="hover:bg-green-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-700">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-3 py-2.5 font-semibold text-gray-900 whitespace-nowrap">{{ $data['kabupaten']->nama_kabupaten }}</td>

                        @foreach($selectedYears as $year)
                        @php
                            $val = $data['years'][$year] ?? 0;
                            $formatted = number_format($val, 2, ',', '.');
                        @endphp
                        <td class="border border-gray-300 px-2 py-2.5 text-center font-medium {{ $val > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                            {{ $formatted }}
                        </td>
                        @endforeach

                        @php
                            $rowTotal     = isset($data['row_total']) ? (float) $data['row_total'] : 0;
                            $rowFormatted = number_format($rowTotal, 2, ',', '.');
                        @endphp
                        <td class="border border-gray-300 px-3 py-2.5 text-center font-bold text-gray-900 min-w-[110px]">
                            {{ $rowFormatted }}
                        </td>

                        <td class="border border-gray-300 px-2 py-2.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1 flex-nowrap overflow-x-auto">
                                @foreach($selectedYears as $year)
                                    @php $record = $data['records'][$year] ?? null; @endphp
                                    @if($record)
                                    <div class="flex gap-1 border border-green-200 bg-green-50 rounded p-1 items-center">
                                        <span class="text-[10px] font-bold text-green-700 mx-1">{{ $year }}</span>
                                        <a href="{{ route('ip.padi.show', $record->id) }}"
                                           class="inline-flex items-center px-1.5 py-1 bg-blue-500 text-white rounded text-xs font-semibold shadow-sm hover:bg-blue-600"
                                           title="Detail {{ $year }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($selectedYears) + 4 }}" class="border border-gray-300 text-center py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-semibold">Data IP Padi belum tersedia</p>
                                <p class="text-xs mt-2 text-gray-400">Data otomatis tersinkronisasi saat KSA Tanam atau LBS diupdate</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if(count($groupedData) > 0)
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-600 to-green-700 text-white font-bold">
                        <td class="border border-green-800 px-2 py-3 text-center" colspan="2">
                            <span class="text-sm uppercase tracking-wide">RATA-RATA</span>
                        </td>
                        @foreach($selectedYears as $year)
                        @php
                            $avg          = $averages[$year] ?? 0;
                            $avgFormatted = number_format($avg, 2, ',', '.');
                        @endphp
                        <td class="border border-green-800 px-2 py-3 text-center text-sm font-extrabold">
                            {{ $avgFormatted }}
                        </td>
                        @endforeach
                        @php $stFormatted = number_format($sumAveragesAll ?? 0, 2, ',', '.'); @endphp
                        <td class="border border-green-800 px-2 py-3 text-center text-sm font-extrabold text-white">
                            {{ $stFormatted }}
                        </td>
                        <td class="border border-green-800 px-2 py-3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    @if(count($groupedData) > 0 && count($selectedYears) > 0)
    @php $latestYear = max($selectedYears); @endphp
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Kabupaten</p>
                    <p class="text-3xl font-bold mt-2">{{ count($groupedData) }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">IP Tertinggi {{ $latestYear }}</p>
                    @php
                        $maxIp        = collect($groupedData)->map(fn($d) => $d['years'][$latestYear] ?? 0)->max() ?? 0;
                        $maxFormatted = floor($maxIp) == $maxIp
                            ? number_format($maxIp, 0, ',', '.')
                            : number_format($maxIp, 2, ',', '.');
                    @endphp
                    <p class="text-3xl font-bold mt-2">{{ $maxFormatted }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rata-rata IP {{ $latestYear }}</p>
                    @php
                        $avgLatest    = $averages[$latestYear] ?? 0;
                        $avgLatestFmt = floor($avgLatest) == $avgLatest
                            ? number_format($avgLatest, 0, ',', '.')
                            : number_format($avgLatest, 2, ',', '.');
                    @endphp
                    <p class="text-3xl font-bold mt-2">{{ $avgLatestFmt }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Tahun Ditampilkan</p>
                    <p class="text-3xl font-bold mt-2">{{ count($selectedYears) }}</p>
                    <p class="text-xs mt-1 opacity-75">Tahun Data</p>
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
.overflow-x-auto::-webkit-scrollbar-track { background: #f0fdf4; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #16a34a; }
</style>
@endsection