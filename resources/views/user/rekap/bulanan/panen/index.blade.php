@extends('layouts.topbar.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                Rekap Bulanan Luas Panen Tahun {{ $tahun }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">Data luas panen padi per kabupaten/kota di Sumatera Selatan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('user.rekap.bulanan.panen.export', ['tahun' => $tahun]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        @forelse($years as $y)
                            <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                        @empty
                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota</label>
                    <select name="search" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                        <option value="">Semua Kabupaten/Kota</option>
                        @foreach($allKabupatens as $kab)
                            <option value="{{ $kab->nama_kabupaten }}" {{ $search == $kab->nama_kabupaten ? 'selected' : '' }}>
                                {{ $kab->nama_kabupaten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
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
                <strong>Informasi:</strong> Data rekap bulanan ini otomatis terupdate setiap ada perubahan data harian • Total = Σ Januari - Desember
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-green-200 to-green-300 text-gray-800 text-center font-bold">
                        <th class="border border-gray-300 px-3 py-3" rowspan="2">No</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold" colspan="12">Luas Panen (Ha) Tahun {{ $tahun }}</th>
                        <th class="border border-gray-300 px-4 py-3 text-center font-bold min-w-[100px]" rowspan="2">TOTAL</th>
                        <th class="border border-gray-300 px-3 py-3 text-center font-bold" rowspan="2">Aksi</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-green-100 to-green-200 text-gray-700 font-semibold">
                        @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'] as $b)
                            <th class="border border-gray-300 px-2 py-2 text-center min-w-[64px]">{{ $b }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($rekaps as $index => $rekap)
                        <tr class="hover:bg-green-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-200 px-3 py-2.5 text-center font-bold text-gray-600">{{ $index + 1 }}</td>
                            <td class="border border-gray-200 px-4 py-2.5 font-bold text-gray-900 whitespace-nowrap">{{ strtoupper($rekap->kabupaten->nama_kabupaten) }}</td>
                            @foreach(['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'] as $bulan)
                                <td class="border border-gray-200 px-3 py-2.5 text-right font-medium {{ $rekap->$bulan > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $rekap->$bulan == 0 ? '0' : (floor($rekap->$bulan) == $rekap->$bulan ? number_format($rekap->$bulan, 0, ',', '.') : number_format($rekap->$bulan, 2, ',', '.')) }}
                                </td>
                            @endforeach
                            <td class="border border-gray-200 px-4 py-2.5 text-right font-bold text-gray-900">{{ $rekap->total == 0 ? '0' : (floor($rekap->total) == $rekap->total ? number_format($rekap->total, 0, ',', '.') : number_format($rekap->total, 2, ',', '.')) }}</td>
                            <td class="border border-gray-200 px-3 py-2.5 text-center whitespace-nowrap">
                                @if($rekap->id)
                                <a href="{{ route('user.rekap.bulanan.panen.show', $rekap->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-xs font-semibold shadow-md">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="border border-gray-200 text-center py-12">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-lg font-semibold">Data luas panen belum tersedia</p>
                                    <p class="text-sm mt-2">untuk tahun {{ $tahun }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($rekaps->count() > 0)
                <tfoot>
                    <tr class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold">
                        <td class="border border-green-400 px-3 py-3 text-center" colspan="2">
                            <span class="text-xs uppercase tracking-wide font-extrabold">JUMLAH</span>
                        </td>
                        @foreach(['total_januari','total_februari','total_maret','total_april','total_mei','total_juni','total_juli','total_agustus','total_september','total_oktober','total_november','total_desember'] as $bulan)
                            <td class="border border-green-400 px-3 py-3 text-right">{{ ($totals->$bulan ?? 0) == 0 ? '0' : (floor($totals->$bulan) == $totals->$bulan ? number_format($totals->$bulan, 0, ',', '.') : number_format($totals->$bulan, 2, ',', '.')) }}</td>
                        @endforeach
                        <td class="border border-green-400 px-4 py-3 text-right font-bold text-white">{{ ($totals->grand_total ?? 0) == 0 ? '0' : (floor($totals->grand_total) == $totals->grand_total ? number_format($totals->grand_total, 0, ',', '.') : number_format($totals->grand_total, 2, ',', '.')) }}</td>
                        <td class="border border-green-400"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    

    @if($rekaps->count() > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Kabupaten</p><p class="text-3xl font-bold mt-2">{{ $rekaps->count() }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Luas Panen Tertinggi</p><p class="text-3xl font-bold mt-2">{{ ($rekaps->max('total') ?? 0) == 0 ? '0' : (floor($rekaps->max('total')) == $rekaps->max('total') ? number_format($rekaps->max('total'), 0, ',', '.') : number_format($rekaps->max('total'), 2, ',', '.')) }}</p><p class="text-xs mt-1 opacity-75">Hektar</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Rata-rata Luas Panen</p><p class="text-3xl font-bold mt-2">{{ ($rekaps->avg('total') ?? 0) == 0 ? '0' : (floor($rekaps->avg('total')) == $rekaps->avg('total') ? number_format($rekaps->avg('total'), 0, ',', '.') : number_format($rekaps->avg('total'), 2, ',', '.')) }}</p><p class="text-xs mt-1 opacity-75">Hektar</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-lime-500 to-lime-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Luas Panen</p><p class="text-3xl font-bold mt-2">{{ ($totals->grand_total ?? 0) == 0 ? '0' : (floor($totals->grand_total) == $totals->grand_total ? number_format($totals->grand_total, 0, ',', '.') : number_format($totals->grand_total, 2, ',', '.')) }}</p><p class="text-xs mt-1 opacity-75">Hektar</p></div>
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
