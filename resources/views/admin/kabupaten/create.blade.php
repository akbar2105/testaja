@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah Kabupaten</h2>

    <form action="{{ route('admin.kabupaten.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="nama_kabupaten" class="block text-gray-700 font-medium mb-2">Nama Kabupaten</label>
            <input type="text"
                   id="nama_kabupaten"
                   name="nama_kabupaten"
                   value="{{ old('nama_kabupaten') }}"
                   placeholder="Contoh: Ogan Komering Ulu"
                   class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('nama_kabupaten') border-red-500 @else border-gray-300 @enderror">
            @error('nama_kabupaten')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">
                Simpan
            </button>
            <a href="{{ route('admin.kabupaten.index') }}"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
