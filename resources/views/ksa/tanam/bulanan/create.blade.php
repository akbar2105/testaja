@extends('layouts.app')

@section('title', 'Tambah KSA Luas Tanam')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('ksa.tanam.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-yellow-600 transition-colors font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Sanding Bulanan
        </a>
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
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Tambah Data KSA</h1>
                    <p class="mt-0.5 text-yellow-100 text-sm font-medium">Luas Tanam Padi &mdash; Input per Kabupaten, Oktober s.d. September</p>
                </div>
            </div>
            <div class="relative mt-5 flex items-center gap-2 text-xs">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">KSA Tanam</span>
                <svg class="w-3 h-3 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">Sanding Bulanan</span>
                <svg class="w-3 h-3 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white text-yellow-700 px-3 py-1 rounded-full font-bold shadow">Tambah</span>
            </div>
        </div>

        <form method="POST" action="{{ route('ksa.tanam.bulanan.store') }}" class="p-8 space-y-7" id="formTambah">
            @csrf

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

            {{-- ── Kabupaten & Tahun ── --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten/Kota <span class="text-red-500">*</span></label>
                    <select name="kabupaten_id" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ old('kabupaten_id', $selectedKabId ?? '') == $kab->id ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Tahun Periode <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-400 ml-1">— tahun Oktober mulai</span>
                    </label>
                    <input type="number" name="tahun" id="inputTahun" required
                           min="2000" max="2100"
                           value="{{ old('tahun', $selectedTahun ?? date('Y')) }}"
                           placeholder="{{ date('Y') }}"
                           class="w-full border-gray-200 rounded-xl shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5 px-3 font-mono"
                           oninput="updatePeriodePreview()">
                    <p class="mt-1.5 text-xs font-semibold" id="periodePreview">
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span id="periodeText">Periode: Okt {{ old('tahun', $selectedTahun ?? date('Y')) }} – Sep {{ (old('tahun', $selectedTahun ?? date('Y'))) + 1 }}</span>
                        </span>
                    </p>
                </div>
            </div>

            {{-- ── 12 Bulan Grid (Okt–Sep) ── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-gray-700">
                        Luas Tanam per Bulan (Ha) <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">— minimal 1 bulan harus diisi</span>
                    </label>
                    <div id="counterBadge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 transition-all">
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
                    <div class="bulan-item rounded-xl border border-gray-200 bg-gray-50 overflow-hidden transition-all duration-200 hover:border-yellow-300 hover:bg-yellow-50/30 hover:shadow-sm">
                        <div class="flex items-center gap-2 px-3 pt-3 pb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700 text-xs font-extrabold flex-shrink-0">{{ $bulanLabel[$b] }}</span>
                            <span class="text-xs font-bold text-gray-700">{{ $nama }}</span>
                        </div>
                        <div class="px-3 pb-3 relative">
                            <input type="number" name="luas_tanam[{{ $b }}]" step="0.01" min="0"
                                   value="{{ old('luas_tanam.'.$b) }}" placeholder="—"
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
                    Simpan Data
                </button>
                <a href="{{ route('ksa.tanam.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
    </div>
</div>

<script>
function countFilled() {
    return [...document.querySelectorAll('.bulan-input')].filter(i => i.value !== '' && parseFloat(i.value) >= 0).length;
}
function updateCounter() {
    const n = countFilled();
    const badge = document.getElementById('counterBadge');
    document.getElementById('counterNum').textContent = n;
    if (n > 0) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
    else { badge.classList.add('hidden'); badge.classList.remove('flex'); }
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
    document.getElementById('formTambah').submit();
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