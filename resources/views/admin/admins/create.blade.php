@extends('layouts.app')

@section('title', 'Tambah Admin')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('admin.admins.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Admin
        </a>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                </svg>
            </div>
            Tambah Admin Baru
        </h1>
        <p class="mt-1 text-sm text-gray-500">Buat akun admin dan tentukan kabupaten yang dikelola</p>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h2 class="text-white font-semibold text-lg">Form Admin</h2>
        </div>

        <form action="{{ route('admin.admins.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Nama lengkap admin"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="email@contoh.com"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Ulangi password"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                </div>
            </div>

            {{-- Kabupaten --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Kabupaten / Akses Data
                </label>
                <select name="kabupaten_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('kabupaten_id') border-red-400 @enderror">
                    <option value="">-- Semua Kabupaten (Tidak Dibatasi) --</option>
                    @foreach($kabupatens as $kab)
                        <option value="{{ $kab->id }}" {{ old('kabupaten_id') == $kab->id ? 'selected' : '' }}>
                            {{ $kab->nama_kabupaten }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-gray-500">
                    Pilih kabupaten untuk membatasi admin hanya mengelola data kabupaten tersebut.
                    Kosongkan jika admin boleh mengakses semua kabupaten.
                </p>
                @error('kabupaten_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Status Akun <span class="text-red-500">*</span>
                </label>
                <select name="is_active"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Info Box --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-xs text-indigo-800 space-y-1">
                        <p class="font-semibold">Tentang Pembatasan Kabupaten:</p>
                        <p>• Jika kabupaten <strong>dipilih</strong>: admin hanya bisa input & edit data kabupaten tersebut.</p>
                        <p>• Jika kabupaten <strong>tidak dipilih</strong>: admin bisa mengakses data semua kabupaten.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.admins.index') }}"
                   class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection