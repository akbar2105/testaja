@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Kecamatan</h2>

    <form action="{{ route('admin.kecamatan.update', $kecamatan->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="kabupaten_id" class="block text-gray-700 font-medium mb-2">Pilih Kabupaten</label>
            <select name="kabupaten_id" id="kabupaten_id"
                    class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                           @error('kabupaten_id') border-red-500 @else border-gray-300 @enderror">
                <option value="">-- Pilih Kabupaten --</option>
                @foreach($kabupaten as $k)
                    <option value="{{ $k->id }}" {{ old('kabupaten_id', $kecamatan->kabupaten_id) == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kabupaten }}
                    </option>
                @endforeach
            </select>
            @error('kabupaten_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama_kecamatan" class="block text-gray-700 font-medium mb-2">Nama Kecamatan</label>
            <input type="text"
                   id="nama_kecamatan"
                   name="nama_kecamatan"
                   value="{{ old('nama_kecamatan', $kecamatan->nama_kecamatan) }}"
                   placeholder="Contoh: Lubuk Linggau"
                   class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                          @error('nama_kecamatan') border-red-500 @else border-gray-300 @enderror">
            @error('nama_kecamatan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">
                Update
            </button>
            <a href="{{ route('admin.kecamatan.index') }}"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
