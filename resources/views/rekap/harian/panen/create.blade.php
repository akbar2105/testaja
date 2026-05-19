@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    Tambah Data Harian Luas Panen
                </h1>
                <p class="mt-2 text-sm text-gray-600">Input data luas panen harian per kecamatan</p>
            </div>
            <a href="{{ route('rekap.harian.panen.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(Auth::user()->isKabupatenRestricted())
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-800">
                Anda login sebagai admin <strong>{{ Auth::user()->kabupaten->nama_kabupaten }}</strong>.
                Data hanya dapat diinput untuk kabupaten dan kecamatan Anda.
            </p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
        <div class="flex">
            <svg class="h-6 w-6 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-xl border border-gray-200">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-xl">
            <h2 class="text-xl font-bold text-white">Form Tambah Data Harian Luas Panen</h2>
        </div>

        <form action="{{ route('rekap.harian.panen.store') }}" method="POST" id="formRekapHarian" class="p-6">
            @csrf

            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-sm text-green-800 font-medium">
                    <strong>Informasi:</strong> Total LTP = Total Panen + OPLAH + Gogo + CSR.
                    <strong>Realisasi</strong> = Target - Total LTP
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" required
                           min="2000" max="2100" placeholder="contoh: {{ date('Y') }}"
                           value="{{ old('tahun', date('Y')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('tahun') border-red-500 @enderror">
                    @error('tahun')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('bulan') border-red-500 @enderror">
                        <option value="">-- Pilih Bulan --</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $month)
                            <option value="{{ $i+1 }}" {{ old('bulan') == ($i+1) ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                    @error('bulan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten <span class="text-red-500">*</span></label>
                    @if(isset($prefilledKecamatan))
                        <input type="text" value="{{ $prefilledKecamatan->kabupaten->nama_kabupaten ?? '' }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                        <input type="hidden" name="kabupaten_id" value="{{ $prefilledKecamatan->kabupaten_id }}">
                    @elseif(Auth::user()->isKabupatenRestricted())
                        <input type="text" value="{{ Auth::user()->kabupaten->nama_kabupaten }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                        <input type="hidden" name="kabupaten_id" value="{{ Auth::user()->kabupaten_id }}">
                    @else
                        <select name="kabupaten_id" id="kabupaten_id" required onchange="loadKecamatan(this.value)" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('kabupaten_id') border-red-500 @enderror">
                            <option value="">-- Pilih Kabupaten --</option>
                            @foreach($kabupatens as $kab)
                                <option value="{{ $kab->id }}" {{ old('kabupaten_id') == $kab->id ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('kabupaten_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                    @if(isset($prefilledKecamatan))
                        <input type="text" value="{{ $prefilledKecamatan->nama_kecamatan ?? '' }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                        <input type="hidden" name="kecamatan_id" value="{{ $prefilledKecamatan->id }}">
                    @else
                        <select name="kecamatan_id" id="kecamatan_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('kecamatan_id') border-red-500 @enderror">
                            @if(Auth::user()->isKabupatenRestricted())
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" {{ old('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            @else
                                <option value="">-- Pilih Kabupaten Dulu --</option>
                            @endif
                        </select>
                    @endif
                    @error('kecamatan_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-green-500">
                    <h3 class="text-lg font-bold text-gray-800">Data Harian (Tanggal 1-31)</h3>
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Total Panen:</span>
                        <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-lg font-bold" id="totalPanenDisplay">0</span>
                        <span class="text-gray-600">Ha</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    @for ($d = 1; $d <= 31; $d++)
                        @php $field = 'tgl_'.$d; @endphp
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tgl {{ $d }}</label>
                            <div class="relative">
                                <input type="number" name="tgl_{{ $d }}" id="tgl_{{ $d }}"
                                       step="0.01" min="0" value="{{ old($field, 0) }}"
                                       class="day-input w-full border-gray-300 rounded px-2 py-1.5 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all @error($field) border-red-500 @enderror">
                                <span class="absolute right-1.5 top-1.5 text-xs text-gray-400 pointer-events-none">Ha</span>
                            </div>
                            @error($field)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endfor
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-purple-500">Data Tambahan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">

                    @foreach(['oplah' => 'Padi OPLAH', 'gogo' => 'Padi Gogo', 'csr' => 'Padi CSR'] as $field => $label)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $label }} (Ha)</label>
                        <input type="number" name="{{ $field }}" id="{{ $field }}"
                               step="0.01" min="0" value="{{ old($field, 0) }}"
                               class="additional-input w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all @error($field) border-red-500 @enderror">
                        @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    @endforeach

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total LTP (Ha) <span class="text-xs text-gray-400 font-normal">(Auto)</span></label>
                        <input type="text" id="total_ltp_display" readonly value="0"
                               class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-purple-700">
                        <p class="text-xs text-gray-500 mt-1">Total + OPLAH + Gogo + CSR</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Target (Ha)</label>
                        <input type="number" name="target" id="target"
                               step="0.01" min="0" value="{{ old('target', 0) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('target') border-red-500 @enderror">
                        @error('target')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Realisasi (Ha) <span class="text-xs text-gray-400 font-normal">(Auto)</span></label>
                        <input type="text" id="realisasi_display" readonly value="0"
                               class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold">
                        <p class="text-xs text-gray-500 mt-1" id="realisasi_formula">Target - Total LTP</p>
                    </div>

                </div>

                <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-3 rounded-r-lg">
                    <p class="text-xs text-green-700 font-medium"><strong>Rumus:</strong> Total LTP = Total Panen + OPLAH + Gogo + CSR | Realisasi = Target - Total LTP</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..."
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ringkasan Input
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-gray-600 font-semibold">LTP Reguler</p>
                        <p class="text-2xl font-bold text-green-700 mt-1" id="summaryTotal">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-gray-600 font-semibold">Total LTP</p>
                        <p class="text-2xl font-bold text-purple-700 mt-1" id="summaryLTP">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-gray-600 font-semibold">Target</p>
                        <p class="text-2xl font-bold text-green-700 mt-1" id="summaryTarget">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-gray-600 font-semibold">Realisasi (Target - LTP)</p>
                        <p class="text-2xl font-bold mt-1" id="summaryRealisasi">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('rekap.harian.panen.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold">Batal</a>
                <button type="submit" id="btnSubmit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg transition-all font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span id="btnText">Simpan Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const IS_RESTRICTED = {{ Auth::user()->isKabupatenRestricted() ? 'true' : 'false' }};

function fmtVal(v) {
    v = parseFloat(v) || 0;
    if (v === 0) return '0';
    if (Number.isInteger(v) || v % 1 === 0) {
        return v.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    return v.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadKecamatan(kabupatenId) {
    if (IS_RESTRICTED === true || IS_RESTRICTED === 'true') return;
    const sel = document.getElementById('kecamatan_id');
    if(!sel) return;
    sel.innerHTML = '<option value="">Loading...</option>';
    sel.disabled = true;
    if (!kabupatenId) {
        sel.innerHTML = '<option value="">-- Pilih Kabupaten Dulu --</option>';
        sel.disabled = false;
        return;
    }
    fetch(`/ajax/get-kecamatan?kabupaten_id=${kabupatenId}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            data.forEach(k => {
                const o = document.createElement('option');
                o.value = k.id;
                o.textContent = k.nama_kecamatan;
                sel.appendChild(o);
            });
            sel.disabled = false;
            const oldVal = "{{ old('kecamatan_id') }}";
            if (oldVal) sel.value = oldVal;
        })
        .catch(() => {
            sel.innerHTML = '<option value="">Error - refresh halaman</option>';
            sel.disabled = false;
        });
}

function hitungSemua() {
    let totalMain = 0;
    for (let d = 1; d <= 31; d++) {
        let el = document.getElementById(`tgl_${d}`);
        if (el) totalMain += (parseFloat(el.value) || 0);
    }

    const tgEl = document.getElementById('target');
    const target = tgEl ? (parseFloat(tgEl.value) || 0) : 0;
    const oplah  = parseFloat(document.getElementById('oplah')?.value || 0);
    const gogo   = parseFloat(document.getElementById('gogo')?.value || 0);
    const csr    = parseFloat(document.getElementById('csr')?.value || 0);
    const totalALL  = totalMain + oplah + gogo + csr;
    const realisasi = target - totalALL;

    const elTotalDisplay = document.getElementById('totalPanenDisplay');
    if(elTotalDisplay) elTotalDisplay.textContent = fmtVal(totalMain);
    
    if(document.getElementById('total_ltp_display')) document.getElementById('total_ltp_display').value = fmtVal(totalALL);
    if(document.getElementById('summaryTotal')) document.getElementById('summaryTotal').textContent = fmtVal(totalMain);
    if(document.getElementById('summaryLTP')) document.getElementById('summaryLTP').textContent = fmtVal(totalALL);
    if(document.getElementById('summaryTarget')) document.getElementById('summaryTarget').textContent = fmtVal(target);

    const elReal = document.getElementById('realisasi_display');
    if (elReal) {
        elReal.value = fmtVal(realisasi);
        elReal.className = elReal.className.replace(/text-(red|green)-600/g, '');
        elReal.classList.add(realisasi < 0 ? 'text-red-600' : 'text-green-600');
    }

    const elSumReal = document.getElementById('summaryRealisasi');
    if (elSumReal) {
        elSumReal.textContent = fmtVal(realisasi);
        elSumReal.className = elSumReal.className.replace(/text-(red|green)-600/g, '');
        elSumReal.classList.add(realisasi < 0 ? 'text-red-600' : 'text-green-600');
    }

    const formulaDisplay = document.getElementById('realisasi_formula');
    if (formulaDisplay) {
        formulaDisplay.textContent = `${fmtVal(target)} - ${fmtVal(totalALL)} = ${fmtVal(realisasi)} Ha`;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof IS_RESTRICTED !== 'undefined' && (!IS_RESTRICTED || IS_RESTRICTED === 'false')) {
        const kabId = document.getElementById('kabupaten_id')?.value;
        if (kabId) loadKecamatan(kabId);
    }

    document.querySelectorAll('.day-input, .additional-input, #target').forEach(el => {
        if(el) el.addEventListener('input', hitungSemua);
    });

    hitungSemua();
});

let formChanged = false;
document.getElementById('formRekapHarian')?.addEventListener('change', () => { formChanged = true; });
window.addEventListener('beforeunload', function (e) {
    if (formChanged) { e.preventDefault(); e.returnValue = ''; }
});

document.getElementById('formRekapHarian')?.addEventListener('submit', function (e) {
    formChanged = false;
    const btn = document.getElementById('btnSubmit');
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        const txt = document.getElementById('btnText');
        if(txt) txt.textContent = 'Menyimpan...';
    }
});
</script>
@endsection