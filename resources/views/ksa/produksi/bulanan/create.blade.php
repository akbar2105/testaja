@extends('layouts.app')

@section('title', 'Tambah KSA Produksi Padi')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('ksa.produksi.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition-colors font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Sanding Bulanan
        </a>
    </div>

    <div class="relative">
        <div class="absolute -top-6 -right-6 w-40 h-40 bg-blue-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-indigo-200 rounded-full opacity-20 blur-3xl pointer-events-none"></div>

    <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <div class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 px-8 py-8 overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 shadow-lg ring-2 ring-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Tambah Data KSA</h1>
                    <p class="mt-0.5 text-blue-100 text-sm font-medium">Produksi Padi &mdash; Input per Kabupaten, Januari s.d. Desember</p>
                </div>
            </div>
            <div class="relative mt-5 flex items-center gap-2 text-xs">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">KSA Produksi</span>
                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full font-semibold">Sanding Bulanan</span>
                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="bg-white text-blue-700 px-3 py-1 rounded-full font-bold shadow">Tambah</span>
            </div>
        </div>

        <form method="POST" action="{{ route('ksa.produksi.bulanan.store') }}" class="p-8 space-y-7" id="formTambah">
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

            {{-- Kabupaten & Tahun --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kabupaten/Kota <span class="text-red-500">*</span></label>
                    <select name="kabupaten_id" required class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}" {{ old('kabupaten_id', $selectedKabId ?? '') == $kab->id ? 'selected' : '' }}>{{ $kab->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="number"
                           name="tahun"
                           value="{{ old('tahun', $selectedTahun ?? date('Y')) }}"
                           min="2000" max="2100"
                           placeholder="Contoh: {{ date('Y') }}"
                           required
                           class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all bg-gray-50 hover:bg-white text-sm py-2.5 px-3">
                </div>
            </div>

            {{-- 12 Bulan Grid (Jan–Des) --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold text-gray-700">
                        Produksi per Bulan (Ton GKG) <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">— minimal 1 bulan harus diisi</span>
                    </label>
                    <div id="counterBadge" class="hidden text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-700 transition-all">
                        <span id="counterNum">0</span> bulan terisi
                    </div>
                </div>
                <div id="alertMinBulan" class="hidden mb-3 rounded-xl bg-amber-50 border border-amber-200 p-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <p class="text-xs font-semibold text-amber-700">Minimal satu bulan harus diisi sebelum menyimpan.</p>
                </div>

                @php
                    $bulanNama = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                  7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    $bulanSingkat = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                                     7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                @endphp
                <div class="grid grid-cols-3 gap-3" id="bulanGrid">
                    @foreach($bulanNama as $b => $nama)
                    <div class="bulan-item rounded-xl border border-gray-200 bg-gray-50 overflow-hidden transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/30 hover:shadow-sm">
                        <div class="flex items-center gap-2 px-3 pt-3 pb-1">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-100 text-blue-600 text-xs font-extrabold flex-shrink-0">{{ $bulanSingkat[$b] }}</span>
                            <span class="text-xs font-bold text-gray-700">{{ $nama }}</span>
                        </div>
                        <div class="px-3 pb-3 relative">
                            <input type="number" name="produksi[{{ $b }}]" step="0.01" min="0"
                                   value="{{ old('produksi.'.$b) }}" placeholder="—"
                                   class="bulan-input w-full border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all bg-white text-sm py-1.5 pr-14 text-right font-mono"
                                   oninput="updateCounter()">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-bold text-blue-300">Ton</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan..."
                          class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all bg-gray-50 hover:bg-white text-sm resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-2"></div>

            <div class="flex gap-3">
                <button type="button" onclick="submitForm()"
                        class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-indigo-700 shadow-lg hover:shadow-blue-200 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Data
                </button>
                <a href="{{ route('ksa.produksi.bulanan.index', ['tahun' => $selectedTahun ?? date('Y')]) }}"
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
            item.classList.add('border-blue-400','bg-blue-50'); item.classList.remove('border-gray-200','bg-gray-50');
        } else {
            item.classList.remove('border-blue-400','bg-blue-50'); item.classList.add('border-gray-200','bg-gray-50');
        }
    });
}
function submitForm() {
    const al = document.getElementById('alertMinBulan');
    if (countFilled() === 0) { al.classList.remove('hidden'); al.classList.add('flex'); document.getElementById('bulanGrid').scrollIntoView({behavior:'smooth',block:'center'}); return; }
    al.classList.add('hidden');
    document.getElementById('formTambah').submit();
}
document.addEventListener('DOMContentLoaded', updateCounter);
</script>
@endsection