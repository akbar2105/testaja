@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="bg-gradient-to-br from-violet-500 via-purple-600 to-indigo-700 rounded-2xl shadow-2xl p-7 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-xl">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6.75v6.75"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black mb-1">Grafik LTT — Luas Panen Padi</h1>
                        <p class="text-violet-100 text-sm font-medium">Harian • Bulanan • Tahunan • Provinsi Sumatera Selatan</p>
                    </div>
                </div>
                <div class="bg-white/15 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20 text-center">
                    <p class="text-violet-100 text-xs font-semibold uppercase">Kabupaten</p>
                    <p class="text-xl font-black">{{ $totalKab }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB --}}
    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-1.5 flex gap-1">
            @php $tabs = [
                'harian'  => ['label'=>'Rekap Harian',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'bulanan' => ['label'=>'Rekap Bulanan', 'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                'tahunan' => ['label'=>'Rekap Tahunan', 'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ]; @endphp
            @foreach($tabs as $key => $t)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                          {{ $tab === $key ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-violet-50 hover:text-violet-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/></svg>
                    {{ $t['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ========================= HARIAN ========================= --}}
    @if($tab === 'harian')
    @php $bnama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
    <div>
        <form method="GET" class="mb-6">
            <input type="hidden" name="tab" value="harian">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun</label>
                    <input type="number" name="tahun_harian" value="{{ $tahunHarian }}" min="2000" max="2099" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-violet-400 font-semibold text-sm w-28">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Bulan</label>
                    <select name="bulan_harian" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-violet-400 font-semibold text-sm bg-white">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bl)
                            <option value="{{ $i+1 }}" {{ $bulanHarian == $i+1 ? 'selected' : '' }}>{{ $bl }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-violet-700 hover:to-indigo-700 shadow transition-all">Tampilkan</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-lg font-black text-white">Luas Panen Harian — {{ $bnama[$bulanHarian] }} {{ $tahunHarian }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="setHarMode('stacked')" id="btn-h-stacked" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/30 text-white border-white/40">Stacked</button>
                    <button onclick="setHarMode('grouped')" id="btn-h-grouped" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/10 text-white/60 border-white/20">Grouped</button>
                    <button onclick="downloadPng('harChart','ltt-panen-harian-{{ $bulanHarian }}-{{ $tahunHarian }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        PNG
                    </button>
                    <a id="pdf-btn-harian" href="{{ route('grafik.ltt.panen.pdf', ['tab'=>'harian','tahun_harian'=>$tahunHarian,'bulan_harian'=>$bulanHarian,'chart_mode'=>'stacked']) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <div style="min-width:{{ $jumlahHariBulan * 40 }}px;position:relative;">
                        <canvas id="harChart" style="height:460px;"></canvas>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                    @foreach($dataHarian as $i => $row)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700">
                            <span class="w-3 h-3 rounded-sm inline-block" style="background:{{ $chartColors[$i % $chartColorsCount] }}"></span>
                            {{ $row['kabupaten'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-black text-white">Rekapitulasi Harian Luas Panen (Ha) — {{ $bnama[$bulanHarian] }} {{ $tahunHarian }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-violet-50">
                            <th class="border border-violet-200 px-3 py-3 text-left font-black text-violet-900 sticky left-0 bg-violet-50 min-w-[150px]">Kabupaten/Kota</th>
                            @for($d = 1; $d <= $jumlahHariBulan; $d++)
                                <th class="border border-violet-200 px-2 py-3 text-center font-black text-violet-900 min-w-[38px]">{{ $d }}</th>
                            @endfor
                            <th class="border border-violet-200 px-3 py-3 text-center font-black text-violet-900 bg-violet-100 min-w-[80px]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataHarian as $index => $row)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-violet-50/30' }} hover:bg-violet-50 transition-colors">
                                <td class="border border-gray-200 px-3 py-2 font-semibold text-gray-900 sticky left-0 {{ $index % 2 == 0 ? 'bg-white' : 'bg-violet-50/30' }}">
                                    <span class="inline-block w-2.5 h-2.5 rounded-sm mr-1.5" style="background:{{ $chartColors[$index % $chartColorsCount] }}"></span>
                                    {{ $row['kabupaten'] }}
                                </td>
                                @for($d = 1; $d <= $jumlahHariBulan; $d++)
                                    @php $v = $row['tgl_'.$d] ?? 0; @endphp
                                    <td class="border border-gray-200 px-2 py-2 text-right {{ $v > 0 ? 'text-gray-800 font-semibold' : 'text-gray-200' }}">{{ fmtVal($v) }}</td>
                                @endfor
                                <td class="border border-violet-200 px-3 py-2 text-right font-bold bg-violet-50 text-violet-800">{{ fmtVal($row['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                            <td class="border border-violet-700 px-3 py-3 font-black sticky left-0 bg-violet-600">TOTAL</td>
                            @for($d = 1; $d <= $jumlahHariBulan; $d++)
                                @php $colTotal = array_sum(array_column($dataHarian, 'tgl_'.$d)); @endphp
                                <td class="border border-violet-700 px-2 py-3 text-right font-bold">{{ fmtVal($colTotal) }}</td>
                            @endfor
                            <td class="border border-violet-700 px-3 py-3 text-right font-black">{{ fmtVal(array_sum($totalHarian)) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================= BULANAN ========================= --}}
    @elseif($tab === 'bulanan')
    <div>
        <form method="GET" class="mb-6">
            <input type="hidden" name="tab" value="bulanan">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Pilih Tahun</label>
                    <select name="tahun_bulanan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-violet-400 font-semibold text-sm bg-white min-w-[120px]">
                        @foreach($tahunTersedia as $t)
                            <option value="{{ $t }}" {{ $tahunBulanan == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-violet-700 hover:to-indigo-700 shadow transition-all">Tampilkan</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <h2 class="text-lg font-black text-white">Luas Panen per Bulan — Tahun {{ $tahunBulanan }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="setBulMode('stacked')" id="btn-b-stacked" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/30 text-white border-white/40">Stacked</button>
                    <button onclick="setBulMode('grouped')" id="btn-b-grouped" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/10 text-white/60 border-white/20">Grouped</button>
                    <button onclick="downloadPng('bulChart','ltt-panen-bulanan-{{ $tahunBulanan }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        PNG
                    </button>
                    <a id="pdf-btn-bulanan" href="{{ route('grafik.ltt.panen.pdf', ['tab'=>'bulanan','tahun_bulanan'=>$tahunBulanan,'chart_mode'=>'stacked']) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto"><div style="min-width:900px;position:relative;"><canvas id="bulChart" style="height:460px;"></canvas></div></div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                    @foreach($dataBulanan as $i => $row)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-semibold text-gray-700">
                            <span class="w-3 h-3 rounded-sm inline-block" style="background:{{ $chartColors[$i % $chartColorsCount] }}"></span>
                            {{ $row['kabupaten'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-black text-white">Tabel Luas Panen Bulanan (Ha) — Tahun {{ $tahunBulanan }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-violet-50">
                            <th class="border border-violet-200 px-4 py-3 text-left font-black text-violet-900 sticky left-0 bg-violet-50">Kabupaten/Kota</th>
                            @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $bl)
                                <th class="border border-violet-200 px-3 py-3 text-center font-black text-violet-900">{{ $bl }}</th>
                            @endforeach
                            <th class="border border-violet-200 px-3 py-3 text-center font-black text-violet-900 bg-violet-100">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataBulanan as $index => $row)
                            @php $rowTotal = array_sum($row['data']); @endphp
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-violet-50/30' }} hover:bg-violet-50 transition-colors">
                                <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900 sticky left-0 {{ $index % 2 == 0 ? 'bg-white' : 'bg-violet-50/30' }}">
                                    <span class="inline-block w-2.5 h-2.5 rounded-sm mr-1.5" style="background:{{ $chartColors[$index % $chartColorsCount] }}"></span>
                                    {{ $row['kabupaten'] }}
                                </td>
                                @foreach($row['data'] as $val)
                                    <td class="border border-gray-200 px-3 py-2.5 text-right {{ $val > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ fmtVal($val) }}</td>
                                @endforeach
                                <td class="border border-violet-200 px-3 py-2.5 text-right font-bold bg-violet-50 text-violet-800">{{ fmtVal($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                            <td class="border border-violet-700 px-4 py-3 font-black">TOTAL</td>
                            @foreach($totalBulanan as $t)
                                <td class="border border-violet-700 px-3 py-3 text-right font-bold">{{ fmtVal($t) }}</td>
                            @endforeach
                            <td class="border border-violet-700 px-3 py-3 text-right font-black">{{ fmtVal(array_sum($totalBulanan)) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================= TAHUNAN ========================= --}}
    @elseif($tab === 'tahunan')
    <div>
        <form method="GET" class="mb-6">
            <input type="hidden" name="tab" value="tahunan">
            <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-200 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Awal</label>
                    <select name="tahun_awal_tahunan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-violet-400 font-semibold text-sm bg-white min-w-[120px]">
                        @foreach($tahunTersedia as $t) <option value="{{ $t }}" {{ $tahunAwalT == $t ? 'selected' : '' }}>{{ $t }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">Tahun Akhir</label>
                    <select name="tahun_akhir_tahunan" class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-violet-400 font-semibold text-sm bg-white min-w-[120px]">
                        @foreach($tahunTersedia as $t) <option value="{{ $t }}" {{ $tahunAkhirT == $t ? 'selected' : '' }}>{{ $t }}</option> @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-violet-700 hover:to-indigo-700 shadow transition-all">Tampilkan</button>
            </div>
        </form>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h2 class="text-lg font-black text-white">Total Luas Panen per Tahun — {{ $tahunAwalT }}-{{ $tahunAkhirT }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="downloadPng('tahChart','ltt-panen-tahunan-{{ $tahunAwalT }}-{{ $tahunAkhirT }}')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        PNG
                    </button>
                    <a href="{{ route('grafik.ltt.panen.pdf', ['tab'=>'tahunan','tahun_awal_tahunan'=>$tahunAwalT,'tahun_akhir_tahunan'=>$tahunAkhirT,'chart_mode'=>'stacked']) }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/40 transition-all border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto"><div style="min-width:900px;"><canvas id="tahChart" style="height:460px;"></canvas></div></div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap justify-center gap-2">
                    @foreach($tahunRangeT as $idx => $t)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200 text-xs font-bold text-gray-700">
                            <span class="w-3 h-3 rounded-sm" style="background:{{ $chartColors[$idx % $chartColorsCount] }}"></span>
                            Tahun {{ $t }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-black text-white">Tabel Total Tahunan (Ha) — {{ $tahunAwalT }}-{{ $tahunAkhirT }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-violet-50">
                            <th class="border border-violet-200 px-4 py-3 text-left font-black text-violet-900">Kabupaten/Kota</th>
                            @foreach($tahunRangeT as $idx => $t)
                                <th class="border border-gray-200 px-3 py-3 text-center font-black text-white" style="background:{{ $chartColors[$idx % $chartColorsCount] }}">{{ $t }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataTahunan as $index => $row)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-violet-50/30' }} hover:bg-violet-50 transition-colors">
                                <td class="border border-gray-200 px-4 py-2.5 font-semibold text-gray-900">{{ $row['kabupaten'] }}</td>
                                @foreach($tahunRangeT as $t)
                                    @php $v = $row[$t] ?? 0; @endphp
                                    <td class="border border-gray-200 px-3 py-2.5 text-right {{ $v > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ fmtVal($v) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                            <td class="border border-violet-700 px-4 py-3 font-black">TOTAL</td>
                            @foreach($tahunRangeT as $t)
                                <td class="border border-violet-700 px-3 py-3 text-right font-bold">{{ fmtVal($totalTahunan[$t] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const COLORS = {!! json_encode($chartColors) !!};

function fmt(v) {
    if (v === null || v === undefined || v <= 0) return '';
    let isD = Math.round(v * 100) / 100 !== Math.round(v);
    if (isD) return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(v);
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(v));
}
function fmtFull(v) {
    let isD = Math.round(v * 100) / 100 !== Math.round(v);
    if (isD) return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(v);
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(v));
}

function downloadPng(id, name) {
    const canvas = document.getElementById(id);
    if (!canvas) return;
    let titleText = name.replace(/-/g, ' ').toUpperCase();
    const container = canvas.closest('.bg-white');
    if (container) { const h2 = container.querySelector('h2'); if (h2) titleText = h2.innerText; }
    
    const chart = Chart.getChart(id);
    if (!chart) return;
    
    const fmt = v => {
        if (!v || v<=0) return '';
        let isD = Math.round(v*100)/100 !== Math.round(v);
        if (isD) return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2, maximumFractionDigits:2}).format(v);
        return new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Math.round(v));
    };

    const cTemp = document.createElement('canvas');
    const ctxTemp = cTemp.getContext('2d');
    ctxTemp.font = 'bold 11px sans-serif';
    
    let items = [];
    if (chart.data.datasets) {
        items = chart.data.datasets.map(ds => {
            const w = ctxTemp.measureText(ds.label).width + 25; 
            let color = ds.backgroundColor;
            if(Array.isArray(color)) color = color[0];
            return { label: ds.label, color: color || '#333', width: w };
        });
    }
    
    // Scale pl / pt up to native pixel equivalent natively if needed
    // However, canvas.width is native pixels!
    const dpr = window.devicePixelRatio || 1;
    const pl = 30 * dpr, pr = 30 * dpr, pt = 70 * dpr;
    const maxWidth = canvas.width + pl + pr - 40 * dpr;
    
    let lines = []; let curLine = []; let curW = 0;
    items.forEach(item => {
        if (curW + item.width*dpr > maxWidth && curLine.length > 0) {
            lines.push({ items: curLine, width: curW });
            curLine = [item]; curW = item.width*dpr;
        } else {
            curLine.push(item); curW += item.width*dpr;
        }
    });
    if (curLine.length > 0) lines.push({ items: curLine, width: curW });
    
    const pb = (40 + lines.length * 20) * dpr; 
    
    const c = document.createElement('canvas');
    const ctx = c.getContext('2d');
    
    c.width = canvas.width + pl + pr;
    c.height = canvas.height + pt + pb;
    
    // Background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, c.width, c.height);
    
    // Title
    ctx.fillStyle = '#111827';
    ctx.font = 'bold ' + (17 * dpr) + 'px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    ctx.fillText(titleText, c.width / 2, 25 * dpr);
    
    // Original chart native draw
    ctx.drawImage(canvas, pl, pt);
    
    // Exact Coordinate mapping
    const scaleX = canvas.width / (chart.width || canvas.clientWidth || 1);
    const scaleY = canvas.height / (chart.height || canvas.clientHeight || 1);
    
    const isStacked = chart.options?.scales?.x?.stacked === true;
    let totals = {}; 
    let allY = [];
    
    chart.data.datasets.forEach((ds, di) => {
        const meta = chart.getDatasetMeta(di);
        meta.data.forEach((bar, idx) => {
            const v = ds.data[idx];
            if (!v || v <= 0) return;
            allY.push(bar.y * scaleY + pt);
        });
    });
    
    chart.data.datasets.forEach((ds, di) => {
        const meta = chart.getDatasetMeta(di);
        meta.data.forEach((bar, idx) => {
            const v = ds.data[idx];
            if (!v || v <= 0) return;
            
            const x = (bar.x * scaleX) + pl;
            const y = (bar.y * scaleY) + pt;
            let base = pt;
            if (bar.base !== undefined) {
                base = (bar.base * scaleY) + pt;
            } else {
                base = (chart.scales.y.getPixelForValue(0) * scaleY) + pt;
            }
            const barH = Math.abs(base - y);
            
            if (isStacked) {
                if (!totals[idx]) totals[idx] = { sum: 0, topY: 999999, x: x };
                totals[idx].sum += v;
                if (y < totals[idx].topY) totals[idx].topY = y;
                
                // Print inside if it fits (like PDF)
                if (true) {
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold ' + (10 * dpr) + 'px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    /* ctx.fillText hidden */
                }
            } else {
                // Grouped (PDF Style)
                ctx.fillStyle = '#111827';
                ctx.font = 'bold ' + (10 * dpr) + 'px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                
                if (y - (3 * dpr) < pt) {
                     ctx.fillStyle = '#ffffff';
                     ctx.textBaseline = 'top';
                     /* ctx.fillText hidden */
                } else {
                     /* ctx.fillText hidden */
                }
            }
        });
    });
    
    if (isStacked) {
        Object.keys(totals).forEach(idx => {
            const tot = totals[idx];
            if (tot.sum > 0) {
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold ' + (11 * dpr) + 'px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                
                if (tot.topY - (4 * dpr) < pt) {
                     ctx.fillStyle = '#ffffff';
                     ctx.textBaseline = 'top';
                     /* ctx.fillText hidden */
                } else {
                     /* ctx.fillText hidden */
                }
            }
        });
    }
    
    // Draw Legend
    ctx.font = 'bold ' + (11 * dpr) + 'px sans-serif';
    let legendY = c.height - pb + (20 * dpr);
    
    lines.forEach(line => {
        let startX = (c.width - line.width) / 2;
        line.items.forEach(item => {
            ctx.fillStyle = item.color;
            ctx.fillRect(startX, legendY - (5 * dpr), 12 * dpr, 12 * dpr); 
            ctx.fillStyle = '#374151';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';
            ctx.fillText(item.label, startX + (18 * dpr), legendY + (1 * dpr));
            startX += item.width * dpr;
        });
        legendY += 20 * dpr;
    });
    
    const a = document.createElement('a');
    a.download = name + '.png';
    a.href = c.toDataURL('image/png', 1.0);
    a.click();
}


function niceMax(rawMax) {
    if (!rawMax || rawMax <= 0) return { max: 8, stepSize: 1 };
    const step = Math.ceil(rawMax / 7) || 1;
    return { max: step * 8, stepSize: step };
}

/* Tombol state */
const btnOn  = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/30 text-white border-white/40';
const btnOff = 'px-3 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white/10 text-white/60 border-white/20';

/* Update href tombol PDF */
function syncPdfBtn(btnId, mode) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const url = new URL(btn.href);
    url.searchParams.set('chart_mode', mode);
    btn.href = url.toString();
}

@if($tab === 'harian')
const hd  = {!! json_encode($dataHarian) !!};
const hmx = {{ $jumlahHariBulan }};
const htot = Array.from({length: hmx}, (_, i) => hd.reduce((s, r) => s + (r['tgl_'+(i+1)] || 0), 0));
let harChart;

function harDs(mode) {
    return hd.map((row, i) => ({
        label: row.kabupaten,
        data: Array.from({length: hmx}, (_, d) => row['tgl_'+(d+1)] || 0),
        backgroundColor: COLORS[i % COLORS.length] + (mode === 'stacked' ? 'E0' : 'CC'),
        borderColor: COLORS[i % COLORS.length],
        borderWidth: 1,
        borderRadius: mode === 'stacked' ? 0 : 3,
        maxBarThickness: mode === 'stacked' ? 38 : 20,
        stack: mode === 'stacked' ? 's' : row.kabupaten
    }));
}

function makeHar(mode) {
    if (harChart) harChart.destroy();
    const labels   = Array.from({length: hmx}, (_, i) => (i+1).toString());
    const datasets = harDs(mode);

    const tp = { id: 'tp2', afterDatasetsDraw(chart) {
        if (mode !== 'stacked') return;
        const {ctx, scales: {x, y}} = chart; ctx.save();
        labels.forEach((_, i) => {
            const v = htot[i]; if (!v || v <= 0) return;
            ctx.font = 'bold 8px sans-serif'; ctx.fillStyle = '#111';
            ctx.textAlign = 'center'; ctx.textBaseline = 'bottom';
            /* ctx.fillText removed */ });
        ctx.restore();
    }};

    const mvH = mode === 'stacked'
        ? Math.max(...htot, 0)
        : Math.max(...hd.flatMap(r => Array.from({length: hmx}, (_, d) => r['tgl_'+(d+1)] || 0)), 0);
    const {max: yMaxH, stepSize: stH} = niceMax(mvH);

    harChart = new Chart(document.getElementById('harChart').getContext('2d'), {
        type: 'bar', data: {labels, datasets}, plugins: [tp],
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: {padding: {top: 20}},
            plugins: {
                legend: {display: false},
                datalabels: {
                    display: false,
                    anchor: ctx => mode === 'stacked' ? 'center' : 'end',
                    align:  ctx => mode === 'stacked' ? 'center' : 'top',
                    color:  ctx => mode === 'stacked' ? '#fff' : '#333',
                    font: {size: mode === 'stacked' ? 6 : 7, weight: 'bold'},
                    formatter: v => v > 0 ? fmt(v) : null,
                    clamp: true, clip: false,
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    callbacks: {
                        title: ctx => 'Tanggal ' + ctx[0].label,
                        label: ctx => ctx.parsed.y > 0 ? ' ' + ctx.dataset.label + ': ' + fmtFull(ctx.parsed.y) + ' Ha' : null,
                        afterBody: ctx => [' ─────────────', ' Total: ' + fmtFull(htot[ctx[0].dataIndex]) + ' Ha']
                    }
                }
            },
            scales: {
                x: {stacked: mode === 'stacked', border: {display:true,color:'#9CA3AF'}, grid: {display:false,drawTicks:false}, ticks: {font:{size:10,weight:'700'},color:'#374151'}},
                y: {stacked: mode === 'stacked', beginAtZero:true, border:{display:true,color:'#6B7280'}, grid:{color:'#E5E7EB',drawTicks:false,borderDash:[5,5]}, max:yMaxH, ticks:{stepSize:stH,callback:v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v),font:{size:10}}}
            }
        }
    });
}

function setHarMode(m) {
    document.getElementById('btn-h-grouped').className = m === 'grouped' ? btnOn : btnOff;
    document.getElementById('btn-h-stacked').className = m === 'stacked' ? btnOn : btnOff;
    syncPdfBtn('pdf-btn-harian', m);
    makeHar(m);
}

/* ── Default awal: GROUPED ── */
document.addEventListener('DOMContentLoaded', () => setHarMode('stacked'));

@elseif($tab === 'bulanan')
const bd = {!! json_encode($dataBulanan) !!};
const bt = {!! json_encode(array_values($totalBulanan)) !!};
const bl = {!! json_encode($bulanLabels) !!};
let bulChart;

function bulDs(mode) {
    return bd.map((row, i) => ({
        label: row.kabupaten, data: row.data,
        backgroundColor: COLORS[i % COLORS.length] + (mode === 'stacked' ? 'E0' : 'CC'),
        borderColor: COLORS[i % COLORS.length],
        borderWidth: 1.5, borderRadius: mode === 'stacked' ? 0 : 3,
        maxBarThickness: mode==='stacked'?38:20,
        categoryPercentage: mode==='stacked'?0.8:0.9,
        barPercentage: mode==='stacked'?0.95:0.85,
        stack: mode === 'stacked' ? 's' : row.kabupaten
    }));
}

function makeBul(mode) {
    if (bulChart) bulChart.destroy();
    const datasets = bulDs(mode);

    const tp = { id: 'tp', afterDatasetsDraw(chart) {
        if (mode !== 'stacked') return;
        const {ctx, scales: {x, y}} = chart; ctx.save();
        bl.forEach((_, i) => {
            const v = bt[i]; if (!v || v <= 0) return;
            ctx.font = 'bold 9px sans-serif'; ctx.fillStyle = '#111';
            ctx.textAlign = 'center'; ctx.textBaseline = 'bottom';
            /* ctx.fillText removed */ });
        ctx.restore();
    }};

    const mvB = mode === 'stacked'
        ? Math.max(...bt, 0)
        : Math.max(...bd.flatMap(r => r.data), 0);
    const {max: yMaxB, stepSize: stB} = niceMax(mvB);

    bulChart = new Chart(document.getElementById('bulChart').getContext('2d'), {
        type: 'bar', data: {labels: bl, datasets}, plugins: [tp],
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: {padding: {top: 20}},
            plugins: {
                legend: {display: false},
                datalabels: {
                    display: false,
                    anchor: ctx => mode === 'stacked' ? 'center' : 'end',
                    align:  ctx => mode === 'stacked' ? 'center' : 'top',
                    color:  ctx => mode === 'stacked' ? '#fff' : '#333',
                    font: {size: mode === 'stacked' ? 7 : 8, weight: 'bold'},
                    formatter: v => v > 0 ? fmt(v) : null,
                    clamp: true, clip: false,
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + fmtFull(ctx.parsed.y) + ' Ha',
                        afterBody: ctx => [' ─────────────', ' Total: ' + fmtFull(bt[ctx[0].dataIndex]) + ' Ha']
                    }
                }
            },
            scales: {
                x: {stacked: mode === 'stacked', border:{display:true,color:'#9CA3AF'}, grid:{display:false,drawTicks:false}, ticks:{font:{size:11,weight:'700'},color:'#374151'}},
                y: {stacked: mode === 'stacked', beginAtZero:true, border:{display:true,color:'#6B7280'}, grid:{color:'#E5E7EB',drawTicks:false,borderDash:[5,5]}, max:yMaxB, ticks:{stepSize:stB,callback:v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v),font:{size:10}}}
            }
        }
    });
}

function setBulMode(m) {
    document.getElementById('btn-b-grouped').className = m === 'grouped' ? btnOn : btnOff;
    document.getElementById('btn-b-stacked').className = m === 'stacked' ? btnOn : btnOff;
    syncPdfBtn('pdf-btn-bulanan', m);
    makeBul(m);
}

/* ── Default awal: GROUPED ── */
document.addEventListener('DOMContentLoaded', () => setBulMode('stacked'));

@elseif($tab === 'tahunan')
document.addEventListener('DOMContentLoaded', function() {
    const labels = {!! json_encode(array_column($dataTahunan,'kabupaten')) !!};
    const tr = {!! json_encode($tahunRangeT) !!};
    const fd = {!! json_encode($dataTahunan) !!};
    const ds = tr.map((t, i) => ({
        label: 'Tahun ' + t, data: fd.map(r => r[t] || 0),
        backgroundColor: COLORS[i % COLORS.length] + 'CC', borderColor: COLORS[i % COLORS.length],
        borderWidth: 1, borderRadius: 4, maxBarThickness: 48, categoryPercentage: .8, barPercentage: .85
    }));
    const mv = Math.max(...fd.flatMap(r => tr.map(t => r[t] || 0)), 0);
    const {max: yMaxT, stepSize: stT} = niceMax(mv);
    const lp = { id: 'lp', afterDatasetsDraw(chart) {
        chart.data.datasets.forEach((d, di) => chart.getDatasetMeta(di).data.forEach((bar, idx) => {
            const v = d.data[idx]; if (!v || v <= 0) return;
            const {ctx} = chart; ctx.save();
            ctx.font = 'bold 7px sans-serif'; ctx.fillStyle = '#111';
            ctx.textAlign = 'center'; ctx.textBaseline = 'bottom';
            /* ctx.fillText removed */ ctx.restore();
        }));
    }};
    new Chart(document.getElementById('tahChart').getContext('2d'), {
        type: 'bar', data: {labels, datasets: ds}, plugins:[],
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: {padding: {top: 20}},
            plugins: {legend:{display:false},  tooltip:{backgroundColor:'rgba(0,0,0,0.85)',callbacks:{label:ctx=>' '+ctx.dataset.label+': '+fmtFull(ctx.parsed.y)+' Ha'}}},
            scales: {
                x: {border:{display:true,color:'#9CA3AF'}, grid:{display:false,drawTicks:false}, ticks:{font:{size:8,weight:'600'},maxRotation:45,minRotation:45}},
                y: {beginAtZero:true, border:{display:true,color:'#6B7280'}, grid:{color:'#E5E7EB',drawTicks:false,borderDash:[5,5]}, max:yMaxT, ticks:{stepSize:stT,callback:v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v),font:{size:10}}}
            }
        }
    });
});
@endif
</script>
@endsection