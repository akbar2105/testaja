@extends('layouts.topbar.app')

@section('content')
    <div class="max-w-full mx-auto px-4 py-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Luas Panen Padi - {{ $months[(int)$bulan] ?? '' }} {{ $tahun }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Data harian luas panen padi per kecamatan di Sumatera Selatan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('user.rekap.harian.panen.export', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="mb-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                        <select name="tahun"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                        <select name="bulan"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            @foreach($months as $key => $m)
                                <option value="{{ $key }}" {{ $key == $bulan ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota</label>
                        <select name="kabupaten_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                            <option value="">Semua Kabupaten/Kota</option>
                            @foreach($kabupatens as $kab)
                                <option value="{{ $kab->id }}" {{ $kab->id == $kabupatenId ? 'selected' : '' }}>
                                    {{ $kab->nama_kabupaten }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- INFO --}}
        <div
            class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-md">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-green-800 font-medium">
                    <strong>Informasi:</strong> Total LTP = LTP Reguler + OPLAH + GOGO + CSR | Realisasi = Target - Total
                    LTP
                </p>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-max text-xs border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-200 to-green-300 text-gray-800 text-center font-semibold">
                            <th class="w-[60px] min-w-[60px] border border-gray-300 px-2 py-2.5 align-middle" rowspan="2">No</th>
                            <th class="w-[220px] min-w-[220px] border border-gray-300 px-3 py-2.5 align-middle" rowspan="2">Kabupaten</th>
                            <th class="w-[60px] min-w-[60px] border border-gray-300 px-2 py-2.5 align-middle" rowspan="2">No</th>
                            <th class="w-[260px] min-w-[260px] border border-gray-300 px-3 py-2.5 align-middle" rowspan="2">Kecamatan</th>
                            <th class="w-[100px] min-w-[100px] border border-gray-300 px-2 py-2.5 align-middle" rowspan="2">Target</th>
                            <th class="border border-gray-300 px-2 py-2.5 align-middle" colspan="31">Tanggal</th>
                            <th class="w-[120px] min-w-[120px] border border-gray-300 px-2 py-2.5 align-middle" rowspan="2">LTP<br>Reguler</th>
                            <th class="border border-gray-300 px-2 py-2.5 align-middle" colspan="3">Total</th>
                            <th class="w-[120px] min-w-[120px] border border-gray-300 px-2 py-2.5 align-middle font-bold text-gray-900" rowspan="2">Total<br>LTP</th>
                            <th class="w-[130px] min-w-[130px] border border-gray-300 px-2 py-2.5 align-middle" rowspan="2">Realisasi -<br>Target
                            </th>

                        </tr>
                        <tr class="bg-gradient-to-r from-green-100 to-green-200 text-gray-700 text-center">
                            @for($d = 1; $d <= 31; $d++)
                                <th class="w-[60px] min-w-[60px] border border-gray-300 px-2 py-2.5 font-medium">{{ $d }}</th>
                            @endfor
                            <th class="w-[90px] min-w-[90px] border border-gray-300 px-2 py-2.5 font-medium">OPLAH</th>
                            <th class="w-[90px] min-w-[90px] border border-gray-300 px-2 py-2.5 font-medium">GOGO</th>
                            <th class="w-[90px] min-w-[90px] border border-gray-300 px-2 py-2.5 font-medium">CSR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $noKabupaten = 0;
                        @endphp

                        @forelse($groupedRekaps as $kabupatenId => $kabupatenRekaps)
                            @php
                                $noKabupaten++;
                                $noKecamatan = 0;
                                $kabTotals = $kabTotalsData[$kabupatenId] ?? ['target' => 0, 'total_panen' => 0, 'oplah' => 0, 'gogo' => 0, 'csr' => 0, 'total_ltp' => 0, 'realisasi' => 0];
                                if(!isset($kabTotals['tgl_1'])) for ($d = 1; $d <= 31; $d++) $kabTotals["tgl_$d"] = 0;
                                $rowspan = count($kabupatenRekaps) + 1;
                            @endphp

                            @foreach($kabupatenRekaps as $idx => $row)
                                @php
                                    $noKecamatan++;
                                    $realisasi = $row->target - $row->total_ltp;
                                    $rowBg = $noKecamatan % 2 === 1 ? 'bg-gray-50' : 'bg-white';
                                @endphp
                                <tr class="{{ $rowBg }} hover:bg-green-50 transition-colors duration-150">
                                    @if($idx === 0)
                                        <td class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-700 bg-gray-100 align-middle"
                                            rowspan="{{ $rowspan }}">{{ $noKabupaten }}</td>
                                        <td class="border border-gray-300 px-2 py-2.5 font-bold text-green-800 whitespace-nowrap bg-gray-100 align-middle"
                                            rowspan="{{ $rowspan }}">{{ strtoupper($row->kabupaten->nama_kabupaten) }}</td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-600">{{ $noKecamatan }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-gray-700 whitespace-nowrap">
                                        {{ strtoupper($row->kecamatan->nama_kecamatan) }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-700">
                                        {{ fmtVal($row->target) }}</td>
                                    @for($d = 1; $d <= 31; $d++)
                                        @php $dv = (float) ($row->{"tgl_$d"} ?? 0); @endphp
                                        <td
                                            class="border border-gray-300 px-2 py-2.5 text-right {{ $dv > 0 ? 'text-gray-700' : 'text-gray-400' }}">
                                            {{ fmtVal($dv) }}</td>
                                    @endfor
                                    <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-700">
                                        {{ fmtVal($row->total_panen) }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-700">
                                        {{ fmtVal($row->oplah) }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-700">
                                        {{ fmtVal($row->gogo) }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-700">
                                        {{ fmtVal($row->csr) }}</td>
                                    <td class="border border-gray-300 px-2 py-2.5 text-right font-bold text-gray-900">
                                        {{ fmtVal($row->total_ltp) }}</td>
                                    <td
                                        class="border border-gray-300 px-2 py-2.5 text-right font-semibold {{ $realisasi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ fmtVal($realisasi) }}</td>
                                </tr>
                            @endforeach

                            {{-- JUMLAH ROW --}}
                            <tr class="bg-gray-200 font-bold text-gray-800">
                                <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-700" colspan="2">Jumlah</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right">{{ fmtVal($kabTotals['target']) }}
                                </td>
                                @for($d = 1; $d <= 31; $d++)
                                    <td class="border border-gray-300 px-2 py-2.5 text-right">{{ fmtVal($kabTotals["tgl_$d"]) }}
                                    </td>
                                @endfor
                                <td class="border border-gray-300 px-2 py-2.5 text-right">
                                    {{ fmtVal($kabTotals['total_panen']) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right">{{ fmtVal($kabTotals['oplah']) }}
                                </td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right">{{ fmtVal($kabTotals['gogo']) }}
                                </td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right">{{ fmtVal($kabTotals['csr']) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right font-bold text-gray-900">
                                    {{ fmtVal($kabTotals['total_ltp']) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right">
                                    {{ fmtVal($kabTotals['realisasi']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="45" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-semibold">Data belum tersedia</p>
                                        <p class="text-sm mt-2">untuk bulan {{ $months[(int)$bulan] ?? '' }}
                                            {{ $tahun }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if(count($groupedRekaps) > 0)
                        <tfoot>
                            <tr class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold">
                                <td class="border border-green-400 px-2 py-2.5 text-center" colspan="4">TOTAL SUMATERA SELATAN
                                </td>
                                <td class="border border-green-400 px-2 py-2.5 text-right">
                                    {{ fmtVal($grandTotals['target']) }}</td>
                                @for($d = 1; $d <= 31; $d++)
                                    <td class="border border-green-400 px-2 py-2.5 text-right">
                                        {{ fmtVal($grandTotals["tgl_$d"]) }}</td>
                                @endfor
                                <td class="border border-green-400 px-2 py-2.5 text-right">{{ fmtVal($grandTotals['total_panen']) }}
                            </td>
                                <td class="border border-green-400 px-2 py-2.5 text-right">{{ fmtVal($grandTotals['oplah']) }}
                                </td>
                                <td class="border border-green-400 px-2 py-2.5 text-right">{{ fmtVal($grandTotals['gogo']) }}
                                </td>
                                <td class="border border-green-400 px-2 py-2.5 text-right">{{ fmtVal($grandTotals['csr']) }}
                                </td>
                                <td class="border border-green-400 px-2 py-2.5 text-right font-bold text-white">
                                    {{ fmtVal($grandTotals['total_ltp']) }}</td>
                                <td class="border border-green-400 px-2 py-2.5 text-right">
                                    {{ fmtVal($grandTotals['realisasi']) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <style>
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f0fdf4;
            border-radius: 4px
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #86efac;
            border-radius: 4px
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #16a34a
        }
    </style>
@endsection