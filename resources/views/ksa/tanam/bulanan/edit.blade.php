@extends('layouts.app')

@section('title', 'Edit KSA Luas Tanam')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('ksa.tanam.bulanan.index', ['tahun' => $tahun]) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-yellow-600 transition-colors font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Sanding Bulanan Okt {{ $tahun }}–Sep {{ $tahun + 1 }}
        </a>
    </div>

    {{-- Info banner --}}
    <div class="mb-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-100 flex-shrink-0">
            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-xs text-yellow-600 font-semibold uppercase tracking-wide">Sedang mengedit</p>
            <p class="text-sm text-yellow-900 font-bold mt-0.5">{{ $kabupaten->nama_kabupaten }} &mdash; Okt {{ $tahun }} – Sep {{ $tahun + 1 }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-yellow-500 font-medium">Bulan terisi</p>
            <p class="text-xl font-extrabold text-yellow-700">{{ count($existingData) }} <span class="text-xs font-semibold text-yellow-400">/ 12</span></p>
        </div>
    </div>

    <div class="relative">
        <div class="absolute -top-6 -right-6 w-40 h-40 bg-yellow-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-orange-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>

    <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- ── HEADER CARD ── --}}
        <div class="relative bg-gradient-to-br from-yellow-500 via-yellow-600 to-orange-600 px-8 py-8 overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 shadow-lg ring-2 ring-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Edit Data KSA</h1>
                    <p class="mt-0.5 text-yellow-100 text-sm font-medium">{{ $kabupaten->nama_kabupaten }} &mdash; Okt {{ $tahun }} – Sep {{ $tahun + 1 }}</p>
                </div>
            </div>
            <div class="relative mt-5 flex items-center gap-2 text-xs">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">KSA Tanam</span>
                <svg class="w-3 h-3 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">Sanding Bulanan</span>
                <svg class="w-3 h-3 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white text-yellow-700 px-3 py-1 rounded-full font-bold shadow">Edit</span>
            </div>
        </div>

        <form method="POST" action="{{ route('ksa.tanam.bulanan.update', ['kabupaten_id' => $kabupaten->id]) }}"
              class="p-8 space-y-7" id="formEdit">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <div><p class="text-sm font-bold text-red-700 mb-1">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Pindah Periode ── --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tahun Periode
                    <span class="text-xs font-normal text-gray-400 ml-1">— ganti jika ingin pindah ke periode lain</span>
                </label>
                <input type="number" name="tahun" id="inputTahun" required
                       min="2000" max="2100"
                       value="{{ old('tahun', $tahun) }}"
                       placeholder="{{ $tahun }}"
                       class="w-full border-gray-200 rounded-xl shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5 px-3 font-mono"
                       oninput="updatePeriodePreview()">
                <div class="mt-1.5 flex items-center justify-between">
                    <p class="text-xs font-semibold" id="periodePreview">
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span id="periodeText">Periode: Okt {{ $tahun }} – Sep {{ $tahun + 1 }}</span>
                        </span>
                    </p>
                    <p class="text-xs text-amber-500 font-medium">⚠ Mengubah tahun akan memindahkan data ke periode baru</p>
                </div>
            </div>

            {{-- ── 12 Bulan Grid (Okt–Sep) ── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-gray-700">
                        Luas Tanam per Bulan (Ha) <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">— minimal 1 bulan harus diisi</span>
                    </label>
                    <div id="counterBadge" class="text-xs font-bold px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 flex items-center gap-1">
                        <span id="counterNum">0</span> bulan terisi
                    </div>
                </div>
                <div id="alertMinBulan" class="hidden mb-3 rounded-xl bg-amber-50 border border-amber-200 p-3 items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <p class="text-xs font-semibold text-amber-700">Minimal satu bulan harus diisi sebelum menyimpan.</p>
                </div>

                @php
                    $bulanGrid  = [10=>'Oktober',11=>'November',12=>'Desember',1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September'];
                    $bulanLabel = [10=>'Okt',11=>'Nov',12=>'Des',1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep'];
                @endphp
                <div class="grid grid-cols-3 gap-3" id="bulanGrid">
                    @foreach($bulanGrid as $b => $nama)
                    @php
                        $existing = $existingData[$b] ?? null;
                        $val      = old('luas_tanam.'.$b, $existing?->luas_tanam ?? '');
                        $hasPrev  = $existing !== null;
                    @endphp
                    <div class="bulan-item rounded-xl border overflow-hidden transition-all duration-200 hover:shadow-sm
                        {{ $val !== '' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 bg-gray-50 hover:border-yellow-300 hover:bg-yellow-50/30' }}">
                        <div class="flex items-center justify-between px-3 pt-3 pb-1">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700 text-xs font-extrabold flex-shrink-0">{{ $bulanLabel[$b] }}</span>
                                <span class="text-xs font-bold text-gray-700">{{ $nama }}</span>
                            </div>
                            @if($hasPrev)
                            <span class="text-xs text-yellow-500 font-semibold bg-yellow-100 px-1.5 py-0.5 rounded-md">Ada data</span>
                            @endif
                        </div>
                        <div class="px-3 pb-3 relative">
                            <input type="number" name="luas_tanam[{{ $b }}]" step="0.01" min="0"
                                   value="{{ $val }}" placeholder="—"
                                   class="bulan-input w-full border-gray-200 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-white text-sm py-1.5 pr-10 text-right font-mono"
                                   oninput="updateCounter()">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-bold text-yellow-400">Ha</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Keterangan ── --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."
                          class="w-full border-gray-200 rounded-xl shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-gray-50 hover:bg-white text-sm resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-2"></div>

            <div class="flex gap-3">
                <button type="button" onclick="submitForm()"
                        class="flex-1 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold rounded-xl hover:from-yellow-600 hover:to-orange-600 shadow-lg hover:shadow-yellow-200 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('ksa.tanam.bulanan.index', ['tahun' => $tahun]) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Batal
                </a>
            </div>
        </form>

        {{-- ── Danger Zone ── --}}
        @if(Auth::user()->canManageData())
        <div class="px-8 pb-8">
            <div class="rounded-xl border border-red-100 bg-red-50/50 p-5">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs font-bold text-red-500 uppercase tracking-wide">Zona Berbahaya</p>
                </div>
                <p class="text-xs text-red-400 mb-4">Hapus semua data tanam <strong>{{ $kabupaten->nama_kabupaten }}</strong> periode <strong>Okt {{ $tahun }} – Sep {{ $tahun + 1 }}</strong>. Tidak dapat dibatalkan.</p>
                <form method="POST" action="{{ route('ksa.tanam.bulanan.destroy', ['kabupaten_id' => $kabupaten->id]) }}"
                      onsubmit="return confirm('Yakin hapus SEMUA data {{ addslashes($kabupaten->nama_kabupaten) }} Okt {{ $tahun }}–Sep {{ $tahun+1 }}?\n\nTidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-red-600 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 text-sm font-semibold transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Semua Data Okt {{ $tahun }} – Sep {{ $tahun + 1 }}
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
    </div>
</div>

<script>
function countFilled() {
    return [...document.querySelectorAll('.bulan-input')].filter(i => i.value !== '' && parseFloat(i.value) >= 0).length;
}
function updateCounter() {
    const n = countFilled();
    document.getElementById('counterNum').textContent = n;
    document.querySelectorAll('.bulan-item').forEach(item => {
        const inp = item.querySelector('.bulan-input');
        if (inp.value !== '' && parseFloat(inp.value) >= 0) {
            item.classList.add('border-yellow-400','bg-yellow-50');
            item.classList.remove('border-gray-200','bg-gray-50');
        } else {
            item.classList.remove('border-yellow-400','bg-yellow-50');
            item.classList.add('border-gray-200','bg-gray-50');
        }
    });
}
function submitForm() {
    const al = document.getElementById('alertMinBulan');
    if (countFilled() === 0) {
        al.classList.remove('hidden'); al.classList.add('flex');
        document.getElementById('bulanGrid').scrollIntoView({behavior:'smooth',block:'center'});
        return;
    }
    al.classList.add('hidden');
    document.getElementById('formEdit').submit();
}
function updatePeriodePreview() {
    const val = parseInt(document.getElementById('inputTahun').value);
    if (!isNaN(val) && val >= 2000 && val <= 2100) {
        document.getElementById('periodeText').textContent = 'Periode: Okt ' + val + ' – Sep ' + (val + 1);
    } else {
        document.getElementById('periodeText').textContent = 'Masukkan tahun yang valid';
    }
}
document.addEventListener('DOMContentLoaded', () => { updateCounter(); updatePeriodePreview(); });
</script>
@endsection