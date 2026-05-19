@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    Edit Data Luas Baku Sawah
                </h1>
                <p class="mt-2 text-sm text-gray-600">Perbarui data luas baku sawah</p>
            </div>

            <a href="{{ route('lbs.index', ['tahun[]' => $lbs->tahun]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ERROR MESSAGES --}}
    @if($errors->any())
        <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-md">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Terjadi Kesalahan:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- CURRENT DATA INFO --}}
    <div class="mb-6 bg-gradient-to-r from-teal-50 to-cyan-50 border-l-4 border-teal-500 p-4 rounded-r-lg shadow-md">
        <div class="flex items-start">
            <svg class="h-6 w-6 text-teal-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-teal-800 font-semibold">Data Saat Ini:</p>
                <p class="mt-1 text-sm text-teal-700">
                    <strong>{{ $lbs->kabupaten->nama_kabupaten }}</strong> &bull; Tahun <strong>{{ $lbs->tahun }}</strong> &bull; Luas: <strong>{{ number_format($lbs->luas_baku_sawah, 2, ',', '.') }} Ha</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Formulir Edit Data
            </h2>
        </div>

        <form action="{{ route('lbs.update', $lbs->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Kabupaten --}}
            <div>
                <label for="kabupaten_id" class="block text-sm font-bold text-gray-700 mb-2">
                    <svg class="w-5 h-5 inline mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Kabupaten/Kota <span class="text-red-500">*</span>
                </label>
                <select name="kabupaten_id" id="kabupaten_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all @error('kabupaten_id') border-red-500 @enderror">
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    @foreach($kabupatens as $kab)
                        <option value="{{ $kab->id }}" {{ (old('kabupaten_id', $lbs->kabupaten_id) == $kab->id) ? 'selected' : '' }}>
                            {{ $kab->nama_kabupaten }}
                        </option>
                    @endforeach
                </select>
                @error('kabupaten_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tahun --}}
            <div>
                <label for="tahun" class="block text-sm font-bold text-gray-700 mb-2">
                    <svg class="w-5 h-5 inline mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="tahun"
                       id="tahun"
                       value="{{ old('tahun', $lbs->tahun) }}"
                       min="2000" max="2100"
                       placeholder="Contoh: {{ date('Y') }}"
                       required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all @error('tahun') border-red-500 @enderror">
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Luas Baku Sawah --}}
            <div>
                <label for="luas_baku_sawah" class="block text-sm font-bold text-gray-700 mb-2">
                    <svg class="w-5 h-5 inline mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Luas Baku Sawah (Ha) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number"
                           name="luas_baku_sawah"
                           id="luas_baku_sawah"
                           step="0.01"
                           value="{{ old('luas_baku_sawah', $lbs->luas_baku_sawah) }}"
                           placeholder="Contoh: 12500.50"
                           required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all pl-4 pr-16 @error('luas_baku_sawah') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 font-semibold">Ha</span>
                    </div>
                </div>
                @error('luas_baku_sawah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    <svg class="w-4 h-4 inline text-teal-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Masukkan luas baku sawah dalam satuan Hektar (Ha). Gunakan titik (.) untuk desimal.
                </p>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">
                    <svg class="w-5 h-5 inline mr-1 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Keterangan (Opsional)
                </label>
                <textarea name="keterangan"
                          id="keterangan"
                          rows="4"
                          placeholder="Tambahkan keterangan atau catatan tambahan (opsional)..."
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $lbs->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Box --}}
            <div class="bg-gradient-to-r from-teal-50 to-cyan-50 border-l-4 border-teal-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-teal-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-teal-800 font-semibold">Informasi Penting:</p>
                        <ul class="mt-2 text-sm text-teal-700 list-disc list-inside space-y-1">
                            <li>Pastikan perubahan kabupaten dan tahun tidak menyebabkan duplikasi data</li>
                            <li>Perubahan data akan mempengaruhi laporan dan statistik</li>
                            <li>Semua field bertanda <span class="text-red-500 font-bold">*</span> wajib diisi</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('lbs.index', ['tahun[]' => $lbs->tahun]) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-lg hover:from-teal-700 hover:to-cyan-700 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Data
                </button>
            </div>
        </form>
    </div>

    {{-- DELETE SECTION --}}
    @if(Auth::user()->canManageData())
    <div class="mt-6">
        <div class="bg-white rounded-xl shadow-xl border border-red-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Zona Bahaya
                </h2>
            </div>

            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Data Luas Baku Sawah</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Setelah data dihapus, data tidak dapat dikembalikan. Pastikan Anda yakin sebelum menghapus data ini.
                        </p>
                        <form action="{{ route('lbs.destroy', $lbs->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('⚠️ PERINGATAN!\n\nYakin ingin menghapus data LBS untuk:\n• Kabupaten: {{ $lbs->kabupaten->nama_kabupaten }}\n• Tahun: {{ $lbs->tahun }}\n• Luas: {{ number_format($lbs->luas_baku_sawah, 2) }} Ha\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!')"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus Data Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
    document.getElementById('luas_baku_sawah').addEventListener('input', function(e) {
        let value = e.target.value;
        value = value.replace(/[^\d.]/g, '');
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        e.target.value = value;
    });
</script>
@endsection