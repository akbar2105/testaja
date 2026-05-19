@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Detail Panen Bulanan – {{ $rekapBulananPanen->kabupaten->nama_kabupaten }}
            </h1>
            <p class="text-gray-600 mt-1">
                Tahun: {{ $rekapBulananPanen->tahun }}
            </p>
        </div>

        <a href="{{ route('rekap.bulanan.panen.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            ← Kembali
        </a>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-sm text-blue-600 font-semibold">Total Januari</div>
            <div class="text-2xl font-bold text-blue-900">
                {{ number_format($rekapBulananPanen->januari ?? 0, 2) }}
            </div>
            <div class="text-xs text-blue-500">Hektar</div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-600 font-semibold">Total Februari</div>
            <div class="text-2xl font-bold text-green-900">
                {{ number_format($rekapBulananPanen->februari ?? 0, 2) }}
            </div>
            <div class="text-xs text-green-500">Hektar</div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-sm text-yellow-600 font-semibold">Total Maret</div>
            <div class="text-2xl font-bold text-yellow-900">
                {{ number_format($rekapBulananPanen->maret ?? 0, 2) }}
            </div>
            <div class="text-xs text-yellow-500">Hektar</div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="text-sm text-purple-600 font-semibold">Total Tahunan</div>
            <div class="text-2xl font-bold text-purple-900">
                {{ number_format($rekapBulananPanen->total ?? 0, 2) }}
            </div>
            <div class="text-xs text-purple-500">Hektar</div>
        </div>
    </div>

    @php
        // Convert bulan to integer untuk menghindari error array key
        $bulanInt = (int) $bulan;
    @endphp

    {{-- FILTER BULAN --}}
    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <form method="GET" class="flex items-center gap-4">
            <label class="font-semibold text-gray-700">Lihat Data Harian Bulan:</label>
            <select name="bulan" 
                    onchange="this.form.submit()" 
                    class="border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500">
                @foreach ($namaBulan as $key => $value)
                    <option value="{{ $key }}" {{ $key == $bulanInt ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- TABEL DATA HARIAN PER KECAMATAN --}}
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-4 py-3 bg-gray-100 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                Data Per Kecamatan - Bulan {{ $namaBulan[$bulanInt] }} {{ $rekapBulananPanen->tahun }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kecamatan
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Panen (Ha)
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Target (Ha)
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Realisasi (Ha)
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($harianData as $index => $row)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $row->kecamatan->nama_kecamatan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                {{ number_format($row->total_panen ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                {{ number_format($row->target ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold
                                {{ ($row->realisasi ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($row->realisasi ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <button onclick="toggleDetail({{ $row->id }})" 
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                    Lihat Detail Tanggal
                                </button>
                            </td>
                        </tr>
                        
                        {{-- ROW DETAIL TANGGAL (Hidden by default) --}}
                        <tr id="detail-{{ $row->id }}" class="hidden bg-blue-50">
                            <td colspan="6" class="px-6 py-4">
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h4 class="font-semibold text-gray-800 mb-3">
                                        Detail Per Tanggal - {{ $row->kecamatan->nama_kecamatan }}
                                    </h4>
                                    
                                    <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-10 gap-3">
                                        @for ($d = 1; $d <= 31; $d++)
                                            @php 
                                                $field = 'tgl_'.$d;
                                                $value = $row->$field ?? 0;
                                            @endphp
                                            <div class="bg-white rounded border {{ $value > 0 ? 'border-green-300' : 'border-gray-200' }} p-2">
                                                <div class="text-xs text-gray-600 mb-1">Tgl {{ $d }}</div>
                                                <div class="text-sm font-bold {{ $value > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ number_format($value, 2) }}
                                                </div>
                                                <div class="text-xs text-gray-400">Ha</div>
                                            </div>
                                        @endfor
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div class="bg-white rounded border border-gray-200 p-3">
                                            <div class="text-xs text-gray-600 mb-1">OPLAH</div>
                                            <div class="text-lg font-bold text-blue-600">
                                                {{ number_format($row->oplah ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="bg-white rounded border border-gray-200 p-3">
                                            <div class="text-xs text-gray-600 mb-1">GOGO</div>
                                            <div class="text-lg font-bold text-green-600">
                                                {{ number_format($row->gogo ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="bg-white rounded border border-gray-200 p-3">
                                            <div class="text-xs text-gray-600 mb-1">Target</div>
                                            <div class="text-lg font-bold text-gray-800">
                                                {{ number_format($row->target ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="bg-white rounded border border-gray-200 p-3">
                                            <div class="text-xs text-gray-600 mb-1">Realisasi</div>
                                            <div class="text-lg font-bold {{ ($row->realisasi ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($row->realisasi ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="font-semibold">Tidak ada data harian</p>
                                <p class="text-sm">Belum ada data panen harian untuk bulan {{ $namaBulan[$bulanInt] }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($harianData->count() > 0)
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-900">
                                TOTAL {{ strtoupper($namaBulan[$bulanInt]) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                {{ number_format($harianData->sum('total_panen'), 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-900">
                                {{ number_format($harianData->sum('target'), 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm 
                                {{ $harianData->sum('realisasi') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($harianData->sum('realisasi'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- RINGKASAN SEMUA BULAN --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Ringkasan Tahunan {{ $rekapBulananPanen->tahun }}
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $months = [
                    'Januari' => $rekapBulananPanen->januari,
                    'Februari' => $rekapBulananPanen->februari,
                    'Maret' => $rekapBulananPanen->maret,
                    'April' => $rekapBulananPanen->april,
                    'Mei' => $rekapBulananPanen->mei,
                    'Juni' => $rekapBulananPanen->juni,
                    'Juli' => $rekapBulananPanen->juli,
                    'Agustus' => $rekapBulananPanen->agustus,
                    'September' => $rekapBulananPanen->september,
                    'Oktober' => $rekapBulananPanen->oktober,
                    'November' => $rekapBulananPanen->november,
                    'Desember' => $rekapBulananPanen->desember,
                ];
            @endphp

            @foreach($months as $month => $value)
                <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition">
                    <div class="text-xs text-gray-600 mb-1">{{ $month }}</div>
                    <div class="text-lg font-bold text-gray-900">
                        {{ number_format($value ?? 0, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- JAVASCRIPT --}}
<script>
function toggleDetail(id) {
    const detailRow = document.getElementById('detail-' + id);
    
    if (detailRow.classList.contains('hidden')) {
        detailRow.classList.remove('hidden');
    } else {
        detailRow.classList.add('hidden');
    }
}
</script>
@endsection