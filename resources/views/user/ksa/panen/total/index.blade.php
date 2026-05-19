@extends('layouts.topbar.app')
@section('title', 'KSA Sanding Total Tahunan Luas Panen')
@section('content')
<div class="max-w-full mx-auto px-4 py-6">
    {{-- ── HEADER ── --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                Sanding Total Tahunan Luas Panen {{ $tahunAwal }}&ndash;{{ $tahunAkhir }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">KSA &mdash; Total Januari&ndash;Desember per kabupaten per tahun &mdash; Satuan: Hektar (Ha)</p>
        </div>
        <a href="{{ route('user.ksa.panen.total.export', ['tahun_awal' => $tahunAwal, 'tahun_akhir' => $tahunAkhir]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
    </div>

    {{-- ── FILTER ── --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Awal</label>
                    <select name="tahun_awal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $tahunAwal ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Akhir</label>
                    <select name="tahun_akhir" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $tahunAkhir ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota</label>
                    <select name="kabupaten_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all">
                        <option value="">— Semua Kabupaten —</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ $kab->id == $kabupatenId ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-violet-500 to-indigo-600 text-white rounded-lg hover:from-violet-600 hover:to-indigo-700 font-semibold shadow-md transition-all">Tampilkan</button>
                    @if($kabupatenId || $tahunAwal != min($availableYears) || $tahunAkhir != max($availableYears))
                    <a href="{{ route('user.ksa.panen.total.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-all whitespace-nowrap">Reset</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ── INFO ── --}}
    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border-l-4 border-violet-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-violet-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-violet-800 font-medium">
                <strong>Sanding Total Tahunan</strong> &mdash; Total LTP <strong>Januari&ndash;Desember</strong> per kabupaten, dihitung otomatis (SUM) dari
                <a href="{{ route('user.ksa.panen.bulanan.index') }}" class="underline font-bold">Sanding Bulanan</a>. Satuan: <strong>Ha</strong>
            </p>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                        <th class="border border-violet-700 px-3 py-3 text-center font-bold" rowspan="2">No</th>
                        <th class="border border-violet-700 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-violet-700 px-4 py-2 text-center font-bold" colspan="{{ count($years) }}">Total Luas Panen (Ha) Jan&ndash;Des</th>
                        <th class="border border-violet-700 px-3 py-3 text-center font-bold min-w-[110px]" rowspan="2">Total Kumulatif</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-violet-500 to-indigo-500 text-white text-xs">
                        @foreach($years as $year)
                        <th class="border border-violet-600 px-2 py-2 text-center font-bold min-w-[80px]">{{ $year }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($filteredKabupatens as $i => $kab)
                    @php $kabData = $rawData->get($kab->id, collect()); @endphp
                    <tr class="hover:bg-violet-50 transition-colors duration-150 {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-600">{{ $i + 1 }}</td>
                        <td class="border border-gray-200 px-4 py-2.5 font-bold text-gray-900 whitespace-nowrap">{{ strtoupper($kab->nama_kabupaten) }}</td>
                        @foreach($years as $year)
                        @php $val = (float) ($kabData->firstWhere('tahun', $year)?->total ?? 0); @endphp
                        <td class="border border-gray-200 px-2 py-2.5 text-right font-medium {{ $val > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                            {{ ($val ?? 0) == 0 ? '0' : (floor($val) == $val ? number_format($val, 0, ',', '.') : number_format($val, 2, ',', '.')) }}
                        </td>
                        @endforeach
                        <td class="border border-violet-200 px-3 py-2.5 text-right font-bold {{ ($totalPerKabupaten[$kab->id] ?? 0) > 0 ? 'text-violet-700 bg-violet-50' : 'text-gray-300' }}">
                            {{ ($totalPerKabupaten[$kab->id] ?? 0) == 0 ? '0' : (floor($totalPerKabupaten[$kab->id] ?? 0) == ($totalPerKabupaten[$kab->id] ?? 0) ? number_format($totalPerKabupaten[$kab->id] ?? 0, 0, ',', '.') : number_format($totalPerKabupaten[$kab->id] ?? 0, 2, ',', '.')) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ count($years) + 3 }}" class="py-10 text-center text-gray-400 font-semibold">Belum ada data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-violet-700 to-indigo-700 text-white font-bold">
                        <td class="border border-violet-800 px-2 py-3 text-center" colspan="2"><span class="text-sm uppercase tracking-wide">SUMATERA SELATAN</span></td>
                        @foreach($years as $year)
                        @php $tot = $totalPerTahun[$year] ?? 0; @endphp
                        <td class="border border-violet-800 px-2 py-3 text-right text-sm font-extrabold">
                            {{ ($tot ?? 0) == 0 ? '0' : (floor($tot) == $tot ? number_format($tot, 0, ',', '.') : number_format($tot, 2, ',', '.')) }}
                        </td>
                        @endforeach
                        <td class="border border-violet-800 px-3 py-3 text-right text-sm font-extrabold bg-violet-900">
                            {{ ($grandTotal ?? 0) == 0 ? '0' : (floor($grandTotal) == $grandTotal ? number_format($grandTotal, 0, ',', '.') : number_format($grandTotal, 2, ',', '.')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <p class="mt-3 text-xs text-gray-400">* Satuan: Hektar (Ha) &nbsp;|&nbsp; Periode: {{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>

    {{-- SUMMARY CARDS --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Kabupaten</p><p class="text-3xl font-bold mt-2">{{ $totalKabupaten }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total LTP {{ $tahunAkhir }}</p>
                    <p class="text-3xl font-bold mt-2">{{ ($totAkhir ?? 0) == 0 ? '0' : (floor($totAkhir ?? 0) == ($totAkhir ?? 0) ? number_format($totAkhir ?? 0, 0, ',', '.') : number_format($totAkhir ?? 0, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">Ha</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Grand Total Kumulatif</p>
                    <p class="text-3xl font-bold mt-2">{{ ($grandTotal ?? 0) == 0 ? '0' : (floor($grandTotal) == $grandTotal ? number_format($grandTotal, 0, ',', '.') : number_format($grandTotal, 2, ',', '.')) }}</p>
                    <p class="text-xs mt-1 opacity-75">{{ $tahunAwal }}&ndash;{{ $tahunAkhir }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-violet-600 to-violet-800 rounded-xl p-6 text-white shadow-xl">
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
</div>
<style>
.overflow-x-auto::-webkit-scrollbar { height: 8px; }
.overflow-x-auto::-webkit-scrollbar-track { background: #f5f3ff; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb { background: #8b5cf6; border-radius: 4px; }
.overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #7c3aed; }
</style>
@endsection
