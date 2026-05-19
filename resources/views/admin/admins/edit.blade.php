@extends('layouts.app')

@section('title', 'Edit Admin')

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
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
            </div>
            Edit Admin: {{ $admin->name }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">Ubah data akun dan kabupaten yang dikelola admin ini</p>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-white font-semibold text-lg">Form Edit Admin</h2>
        </div>

        <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password (opsional) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Password Baru
                        <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password"
                           placeholder="Password baru..."
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Ulangi password baru..."
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all">
                </div>
            </div>

            {{-- Kabupaten --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Kabupaten / Akses Data
                </label>
                <select name="kabupaten_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all @error('kabupaten_id') border-red-400 @enderror">
                    <option value="">-- Semua Kabupaten (Tidak Dibatasi) --</option>
                    @foreach($kabupatens as $kab)
                        <option value="{{ $kab->id }}"
                            {{ old('kabupaten_id', $admin->kabupaten_id) == $kab->id ? 'selected' : '' }}>
                            {{ $kab->nama_kabupaten }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-gray-500">
                    Ubah kabupaten untuk mengubah batasan akses admin ini.
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
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all">
                    <option value="1" {{ old('is_active', $admin->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $admin->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Info current --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-xs text-gray-600 space-y-1">
                <p class="font-semibold text-gray-700 mb-1">Info Akun Saat Ini:</p>
                <p>Akses: <strong>{{ $admin->kabupaten_label }}</strong></p>
                <p>Status: <strong>{{ $admin->status_label }}</strong></p>
                <p>Dibuat: <strong>{{ $admin->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</strong></p>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.admins.index') }}"
                   class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg hover:from-yellow-600 hover:to-orange-600 shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection