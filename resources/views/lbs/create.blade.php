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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    Tambah Data Luas Baku Sawah
                </h1>
                <p class="mt-2 text-sm text-gray-600">Tambahkan data luas baku sawah untuk kabupaten/kota</p>
            </div>
            <a href="{{ route('lbs.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 shadow-lg transition-all font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- INFO admin kabupaten --}}
    @if(Auth::user()->isKabupatenRestricted())
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-blue-800">
                Anda login sebagai admin <strong>{{ Auth::user()->kabupaten->nama_kabupaten }}</strong>.
                Data hanya dapat diinput untuk kabupaten Anda.
            </p>
        </div>
    </div>
    @endif

    {{-- ERROR --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Formulir Data Luas Baku Sawah</h2>
        </div>

        <form action="{{ route('lbs.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            {{-- Kabupaten --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Kabupaten/Kota <span class="text-red-500">*</span>
                </label>

                @if(Auth::user()->isKabupatenRestricted())
                    <input type="hidden" name="kabupaten_id" value="{{ Auth::user()->kabupaten_id }}">
                    <div class="flex items-center gap-2 w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2.5 cursor-not-allowed">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">{{ Auth::user()->kabupaten->nama_kabupaten }}</span>
                        <span class="ml-auto text-xs text-gray-400 italic">Terkunci</span>
                    </div>
                @else
                    <select name="kabupaten_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all @error('kabupaten_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($kabupatens as $kab)
                            <option value="{{ $kab->id }}"
                                {{ old('kabupaten_id', $defaultKabupatenId) == $kab->id ? 'selected' : '' }}>
                                {{ $kab->nama_kabupaten }}
                            </option>
                        @endforeach
                    </select>
                @endif

                @error('kabupaten_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tahun --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="tahun"
                       value="{{ old('tahun', date('Y')) }}"
                       min="2000" max="2100"
                       placeholder="Contoh: {{ date('Y') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all @error('tahun') border-red-500 @enderror">
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Luas Baku Sawah --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Luas Baku Sawah (Ha) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" name="luas_baku_sawah" step="0.01"
                           value="{{ old('luas_baku_sawah') }}" placeholder="Contoh: 12500.50"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all pr-12 @error('luas_baku_sawah') border-red-500 @enderror">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Ha</span>
                </div>
                @error('luas_baku_sawah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="4" placeholder="Tambahkan keterangan (opsional)..."
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('lbs.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-semibold">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-lg hover:from-teal-700 hover:to-cyan-700 shadow-lg transition-all font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection