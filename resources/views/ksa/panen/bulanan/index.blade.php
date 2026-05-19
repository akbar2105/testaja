@extends('layouts.app')
@section('title', 'KSA Sanding Bulanan Luas Panen ' . $tahun)
@section('content')
<div class="max-w-full mx-auto px-4 py-6">
    {{-- ── HEADER ── --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                Luas Panen Padi (Hektar) Sanding Januari-Desember {{ $tahun }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">KSA &mdash; Input data per kabupaten per bulan &bull; Data ini menjadi sumber Sanding Tahunan &amp; Total Tahunan</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if(Auth::user()->canManageData())
            <a href="{{ route('ksa.panen.bulanan.create', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-500 to-indigo-600 text-white rounded-lg hover:from-violet-600 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Data
            </a>
            @endif
            <a href="{{ route('ksa.panen.bulanan.export', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ── FLASH ── --}}
    @if(session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200">{{ $errors->first() }}</div>
    @endif

    {{-- ── FILTER ── --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tahun</label>
                    <select name="tahun" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all text-sm">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kabupaten/Kota</label>
                    <select name="kabupaten_id" class="w-full h-10 border-gray-300 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all text-sm">
                        <option value="">Semua Kabupaten</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ $kab->id == ($kabupatenId ?? null) ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">&nbsp;</label>
                    <div class="flex gap-2 h-10">
                        <button type="submit" class="flex-1 h-10 bg-gradient-to-r from-violet-500 to-indigo-600 text-white rounded-lg hover:from-violet-600 hover:to-indigo-700 font-semibold shadow-md transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Tampilkan
                        </button>
                        <a href="{{ route('ksa.panen.bulanan.index') }}" class="h-10 px-4 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition-all whitespace-nowrap">Reset</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- ── INFO ── --}}
    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border-l-4 border-violet-500 p-4 mb-6 rounded-r-lg shadow-md">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-violet-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-violet-800 font-medium">
                <strong>Sanding Bulanan Tahun {{ $tahun }}</strong> &mdash; Data ini otomatis muncul di
                <a href="{{ route('ksa.panen.index') }}" class="underline font-bold">Sanding Tahunan per Bulan</a> dan
                <a href="{{ route('ksa.panen.total.index') }}" class="underline font-bold">Sanding Total Tahunan</a>.
                Satuan: <strong>Hektar (Ha)</strong>
            </p>
        </div>
    </div>

    {{-- ── TABEL PIVOT ── --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-violet-500 to-indigo-600 text-white">
                        <th class="border border-violet-600 px-3 py-3 text-center font-bold" rowspan="2">No</th>
                        <th class="border border-violet-600 px-4 py-3 text-center font-bold min-w-[160px]" rowspan="2">Kabupaten/Kota</th>
                        <th class="border border-violet-600 px-4 py-2 text-center font-bold" colspan="12">Luas Panen Padi (Hektar) Tahun {{ $tahun }}</th>
                        <th class="border border-violet-600 px-4 py-3 text-center font-bold min-w-[100px]" rowspan="2">Total</th>
                        @if(Auth::user()->canManageData())
                        <th class="border border-violet-600 px-3 py-3 text-center font-bold" rowspan="2">Aksi</th>
                        @endif
                    </tr>
                    <tr class="bg-gradient-to-r from-violet-400 to-indigo-500 text-white text-xs">
                        @foreach($bulanList as $b => $nama)
                        <th class="border border-violet-500 px-2 py-2 text-center font-bold min-w-[64px]">{{ substr($nama,0,3) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($filteredKabupatens as $i => $kab)
                    @php
                        $kabRows  = $rows->get($kab->id, collect());
                        $hasData  = $kabRows->isNotEmpty();
                        $totalKab = $kabRows->sum('luas_panen');
                    @endphp
                    <tr class="hover:bg-violet-50 transition-colors duration-150 {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-200 px-3 py-2.5 text-center font-semibold text-gray-600">{{ $i + 1 }}</td>
                        <td class="border border-gray-200 px-4 py-2.5 font-bold text-gray-900 whitespace-nowrap">{{ $kab->nama_kabupaten }}</td>
                        @foreach($bulanList as $b => $nama)
                        @php $val = $kabRows->firstWhere('bulan', $b)?->luas_panen ?? null; @endphp
                        <td class="border border-gray-200 px-2 py-2.5 text-right {{ $val > 0 ? 'text-gray-900 font-medium' : 'text-gray-300' }}">
                            {{ fmtVal($val) }}
                        </td>
                        @endforeach
                        <td class="border border-violet-200 px-3 py-2.5 text-right font-bold {{ $totalKab > 0 ? 'text-violet-700 bg-violet-50' : 'text-gray-300' }}">
                            {{ fmtVal($totalKab) }}
                        </td>
                        @if(Auth::user()->canManageData())
                        <td class="border border-gray-200 px-2 py-2 text-center whitespace-nowrap">
                            @if($hasData)
                            @if(Auth::user()->canAccessKabupaten($kab->id))
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('ksa.panen.bulanan.edit', ['kabupaten_id' => $kab->id, 'tahun' => $tahun]) }}" class="inline-flex items-center px-2 py-1 bg-violet-500 text-white rounded hover:bg-violet-600 text-xs font-semibold transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('ksa.panen.bulanan.destroy', ['kabupaten_id' => $kab->id]) }}" class="inline" onsubmit="return confirm('Hapus semua data {{ addslashes($kab->nama_kabupaten) }} tahun {{ $tahun }}?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs font-semibold transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                            @endif
                            
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ Auth::user()->canManageData() ? 16 : 15 }}" class="py-10 text-center text-gray-400 font-semibold">Belum ada data kabupaten</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold">
                        <td colspan="2" class="border border-violet-700 px-4 py-3 text-center font-extrabold tracking-wide uppercase">SUMATERA SELATAN</td>
                        @foreach($bulanList as $b => $nama)
                        @php $tot = $totalPerBulan->get($b, 0); @endphp
                        <td class="border border-violet-700 px-2 py-3 text-right font-extrabold">
                            {{ fmtVal($tot) }}
                        </td>
                        @endforeach
                        <td class="border border-violet-700 px-3 py-3 text-right font-extrabold bg-violet-900">
                            {{ ($totalPerBulan->sum() ?? 0) == 0 ? '0' : (floor($totalPerBulan->sum()) == $totalPerBulan->sum() ? number_format($totalPerBulan->sum(), 0, ',', '.') : number_format($totalPerBulan->sum(), 2, ',', '.')) }}
                        </td>
                        @if(Auth::user()->canManageData())
                        <td class="border border-violet-700 px-2 py-3"></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <p class="mt-3 text-xs text-gray-400">* Satuan: Hektar (Ha) &nbsp;|&nbsp; Tahun: {{ $tahun }}</p>

    {{-- ── SUMMARY CARDS — urutan & warna sama persis dengan KSA Tahunan ── --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Card 1: Total Kabupaten — violet-500 to indigo-600 ✓ --}}
        <div class="bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div><p class="text-sm opacity-90">Total Kabupaten</p><p class="text-3xl font-bold mt-2">{{ $filteredKabupatens->count() }}</p></div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            </div>
        </div>

        {{-- Card 2: LTP Tertinggi — purple-500 to purple-700 ✓ --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">LTP Tertinggi {{ $tahun }}</p>
                    <p class="text-3xl font-bold mt-2">{{ fmtVal($maxBulanVal) }}</p>
                    <p class="text-xs mt-1 opacity-75">{{ $maxBulanNama }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
            </div>
        </div>

        {{-- Card 3: Total LTP tahun ini — indigo-500 to indigo-700 ✓ --}}
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total LTP {{ $tahun }}</p>
                    <p class="text-3xl font-bold mt-2">{{ fmtVal($sumTotal) }}</p>
                    <p class="text-xs mt-1 opacity-75">Hektar (Jan&ndash;Des)</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
        </div>

        {{-- Card 4: Total Record — violet-600 to violet-800 ✓ --}}
        <div class="bg-gradient-to-br from-violet-600 to-violet-800 rounded-xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Record</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalDataRecords }}</p>
                    <p class="text-xs mt-1 opacity-75">Tahun {{ $tahun }}</p>
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