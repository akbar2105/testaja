@extends('layouts.app')

@section('title', 'Tambah KSA Luas Panen')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('ksa.panen.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-violet-600 transition-colors font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Sanding Bulanan
        </a>
    </div>

    {{-- Decorative blobs --}}
    <div class="relative">
        <div class="absolute -top-6 -right-6 w-40 h-40 bg-violet-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-indigo-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>

    <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="relative bg-gradient-to-br from-violet-500 via-violet-600 to-indigo-700 px-8 py-8 overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 shadow-lg ring-2 ring-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Tambah Data KSA</h1>
                    <p class="mt-0.5 text-violet-200 text-sm font-medium">Luas Panen Padi &mdash; Input per Kabupaten, semua bulan sekaligus</p>
                </div>
            </div>

            <div class="relative mt-5 flex items-center gap-2 text-xs">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">KSA Panen</span>
                <svg class="w-3 h-3 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">Sanding Bulanan</span>
                <svg class="w-3 h-3 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white text-violet-700 px-3 py-1 rounded-full font-bold shadow">Tambah</span>
            </div>
        </div>

        <form method="POST" action="{{ route('ksa.panen.bulanan.store') }}" class="p-8 space-y-7" id="formTambah">
            @csrf

            @if($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-700 mb-1">Terdapat kesalahan input:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-sm text-red-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Kabupaten & Tahun --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Kabupaten/Kota <span class="text-red-500">*</span>
                    </label>
                    <select name="kabupaten_id" required
                            class="w-full border-gray-200 rounded-xl shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}"
                                {{ old('kabupaten_id', $selectedKabId ?? '') == $kab->id ? 'selected' : '' }}>
                                {{ $kab->nama_kabupaten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="tahun"
                           value="{{ old('tahun', $selectedTahun ?? date('Y')) }}"
                           min="2000" max="2100"
                           placeholder="Contoh: {{ date('Y') }}"
                           required
                           class="w-full border-gray-200 rounded-xl shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5 px-3">
                </div>
            </div>

            {{-- 12 Bulan Grid --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-gray-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Luas Panen per Bulan (Ha)
                        <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">— minimal 1 bulan harus diisi</span>
                    </label>
                    <div id="counterBadge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-violet-100 text-violet-700 transition-all">
                        <span id="counterNum">0</span> bulan terisi
                    </div>
                </div>

                <div id="alertMinBulan" class="hidden mb-3 rounded-xl bg-amber-50 border border-amber-200 p-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs font-semibold text-amber-700">Minimal satu bulan harus diisi sebelum menyimpan.</p>
                </div>

                <div class="grid grid-cols-3 gap-3" id="bulanGrid">
                    @php
                        $bulanNama = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                      7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @foreach($bulanNama as $b => $nama)
                    <div class="bulan-item rounded-xl border border-gray-200 bg-gray-50 overflow-hidden transition-all duration-200 hover:border-violet-300 hover:bg-violet-50/30 hover:shadow-sm">
                        <div class="flex items-center gap-2 px-3 pt-3 pb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-violet-100 text-violet-600 text-xs font-extrabold flex-shrink-0">{{ $b }}</span>
                            <span class="text-xs font-bold text-gray-700">{{ $nama }}</span>
                        </div>
                        <div class="px-3 pb-3 relative">
                            <input type="number"
                                   name="luas_panen[{{ $b }}]"
                                   step="0.01" min="0"
                                   value="{{ old('luas_panen.'.$b) }}"
                                   placeholder="—"
                                   class="bulan-input w-full border-gray-200 rounded-lg shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all bg-white text-sm py-1.5 pr-10 text-right font-mono"
                                   oninput="updateCounter()">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-bold text-violet-300">Ha</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Keterangan <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                </label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan jika diperlukan..."
                          class="w-full border-gray-200 rounded-xl shadow-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all bg-gray-50 hover:bg-white text-sm resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-2"></div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button type="button" onclick="submitForm()"
                        class="flex-1 py-3 bg-gradient-to-r from-violet-500 to-indigo-600 text-white font-bold rounded-xl hover:from-violet-600 hover:to-indigo-700 shadow-lg hover:shadow-violet-200 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Data
                </button>
                <a href="{{ route('ksa.panen.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
    </div>
</div>

<script>
function countFilled() {
    return [...document.querySelectorAll('.bulan-input')]
        .filter(i => i.value !== '' && parseFloat(i.value) >= 0).length;
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
            item.classList.add('border-violet-400','bg-violet-50');
            item.classList.remove('border-gray-200','bg-gray-50');
        } else {
            item.classList.remove('border-violet-400','bg-violet-50');
            item.classList.add('border-gray-200','bg-gray-50');
        }
    });
}
function submitForm() {
    const alert = document.getElementById('alertMinBulan');
    if (countFilled() === 0) {
        alert.classList.remove('hidden'); alert.classList.add('flex');
        document.getElementById('bulanGrid').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    alert.classList.add('hidden');
    document.getElementById('formTambah').submit();
}
document.addEventListener('DOMContentLoaded', updateCounter);
</script>
@endsection