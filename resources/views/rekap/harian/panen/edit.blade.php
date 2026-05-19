@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-700 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    Edit Data Harian Luas Panen
                </h1>
                <p class="mt-2 text-sm text-gray-600">Perbarui data luas panen harian per kecamatan</p>
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
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 rounded-t-xl">
            <h2 class="text-xl font-bold text-white">Form Edit Data Harian Luas Panen</h2>
        </div>

        <form action="{{ route('rekap.harian.panen.update', $rekap->id) }}" method="POST" id="formRekapHarian" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
                <p class="text-sm text-yellow-800 font-medium">
                    <strong>Informasi:</strong> Total LTP = Total Panen + OPLAH + Gogo + CSR.
                    <strong>Realisasi</strong> = Target - Total LTP
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="text" name="tahun" value="{{ $rekap->tahun }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bulan <span class="text-red-500">*</span></label>
                    @php $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                    <input type="text" value="{{ $monthNames[$rekap->bulan] ?? '' }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                    <input type="hidden" name="bulan" value="{{ $rekap->bulan }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ $rekap->kabupaten->nama_kabupaten ?? '' }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                    <input type="hidden" name="kabupaten_id" value="{{ $rekap->kabupaten_id }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ $rekap->kecamatan->nama_kecamatan ?? '' }}" readonly class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold text-gray-700">
                    <input type="hidden" name="kecamatan_id" value="{{ $rekap->kecamatan_id }}">
                </div>

            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b-2 border-yellow-500">
                    <h3 class="text-lg font-bold text-gray-800">Data Harian (Tanggal 1-31)</h3>
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Total Panen:</span>
                        <span class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg font-bold" id="totalPanenDisplay">0</span>
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
                                       step="0.01" min="0" value="{{ old($field, (float)($rekap->$field ?? 0)) }}"
                                       class="day-input w-full border-gray-300 rounded px-2 py-1.5 text-sm focus:border-yellow-500 focus:ring-1 focus:ring-yellow-200 transition-all @error($field) border-red-500 @enderror">
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
                               step="0.01" min="0" value="{{ old($field, (float)($rekap->$field ?? 0)) }}"
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
                               step="0.01" min="0" value="{{ old('target', (float)($rekap->target ?? 0)) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('target') border-red-500 @enderror">
                        @error('target')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Realisasi (Ha) <span class="text-xs text-gray-400 font-normal">(Auto)</span></label>
                        <input type="text" id="realisasi_display" readonly value="0"
                               class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed font-semibold">
                        <p class="text-xs text-gray-500 mt-1" id="realisasi_formula">Target - Total LTP</p>
                    </div>

                </div>

                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r-lg">
                    <p class="text-xs text-yellow-700 font-medium"><strong>Rumus:</strong> Total LTP = Total Panen + OPLAH + Gogo + CSR | Realisasi = Target - Total LTP</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..."
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $rekap->keterangan) }}</textarea>
                @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Informasi Data
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <p class="text-gray-600 font-semibold">LTP Reguler</p>
                        <p class="text-2xl font-bold text-yellow-700 mt-1" id="summaryTotal">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <p class="text-gray-600 font-semibold">Total LTP</p>
                        <p class="text-2xl font-bold text-purple-700 mt-1" id="summaryLTP">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <p class="text-gray-600 font-semibold">Target</p>
                        <p class="text-2xl font-bold text-yellow-700 mt-1" id="summaryTarget">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <p class="text-gray-600 font-semibold">Realisasi (Target - LTP)</p>
                        <p class="text-2xl font-bold mt-1" id="summaryRealisasi">0</p>
                        <p class="text-xs text-gray-500 mt-1">Hektar (Ha)</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div>
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('deleteForm').submit();"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg transition-all font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Data
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('rekap.harian.panen.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold">Batal</a>
                    <button type="submit" id="btnSubmit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg hover:from-yellow-700 hover:to-yellow-800 shadow-lg transition-all font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span id="btnText">Update Data</span>
                    </button>
                </div>
            </div>
        </form>
        <form id="deleteForm" action="{{ route('rekap.harian.panen.destroy', $rekap->id) }}" method="POST" style="display:none;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
            @csrf
            @method('DELETE')
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
        elReal.classList.add(realisasi < 0 ? 'text-red-600' : 'text-yellow-600');
    }

    const elSumReal = document.getElementById('summaryRealisasi');
    if (elSumReal) {
        elSumReal.textContent = fmtVal(realisasi);
        elSumReal.className = elSumReal.className.replace(/text-(red|green)-600/g, '');
        elSumReal.classList.add(realisasi < 0 ? 'text-red-600' : 'text-yellow-600');
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