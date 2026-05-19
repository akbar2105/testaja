@extends('layouts.app')

@section('title', 'Detail Admin')

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
        <h1 class="text-2xl font-bold text-gray-900">Detail Admin</h1>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

        {{-- Avatar & nama --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
            <div class="h-20 w-20 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-3">
                {{ strtoupper(substr($admin->name, 0, 1)) }}
            </div>
            <h2 class="text-white text-xl font-bold">{{ $admin->name }}</h2>
            <p class="text-white/80 text-sm mt-1">{{ $admin->email }}</p>
        </div>

        {{-- Detail info --}}
        <div class="p-6 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Role</p>
                    <p class="font-semibold text-gray-900">{{ $admin->role_name }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Status</p>
                    @if($admin->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">● Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">● Nonaktif</span>
                    @endif
                </div>
            </div>

            {{-- Kabupaten / Akses --}}
            <div class="rounded-lg p-4 {{ $admin->isKabupatenRestricted() ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200' }}">
                <p class="text-xs font-medium uppercase tracking-wide mb-2 {{ $admin->isKabupatenRestricted() ? 'text-blue-600' : 'text-green-600' }}">
                    Akses Kabupaten
                </p>
                @if($admin->isKabupatenRestricted())
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-blue-900">{{ $admin->kabupaten->nama_kabupaten }}</p>
                            <p class="text-xs text-blue-600 mt-0.5">Hanya dapat mengelola data kabupaten ini</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        </svg>
                        <div>
                            <p class="font-bold text-green-900">Semua Kabupaten</p>
                            <p class="text-xs text-green-600 mt-0.5">Dapat mengakses data seluruh kabupaten</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Dibuat</p>
                    <p class="text-gray-800">{{ $admin->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Terakhir Diperbarui</p>
                    <p class="text-gray-800">{{ $admin->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('admin.admins.edit', $admin) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 text-sm font-semibold transition-all shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Admin
            </a>
            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                  onsubmit="return confirm('Hapus admin {{ $admin->name }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm font-semibold transition-all shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Admin
                </button>
            </form>
        </div>
    </div>

</div>
@endsection