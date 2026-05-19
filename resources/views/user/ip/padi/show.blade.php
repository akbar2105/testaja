@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-700 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    Detail Indeks Pertanaman Padi
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $ip->kabupaten->nama_kabupaten }} • Tahun {{ $ip->tahun }}
                </p>
            </div>

            <div class="flex gap-2">

                {{-- Back Button --}}
                <a href="{{ route('user.ip.padi.index', ['tahun[]' => $ip->tahun]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-md">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN - Info Card --}}
        <div class="lg:col-span-1">
            {{-- Info Card --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informasi Data
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Kabupaten --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Kabupaten/Kota</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $ip->kabupaten->nama_kabupaten }}</p>
                        </div>
                    </div>

                    {{-- Tahun --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Tahun IP</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $ip->tahun }}</p>
                        </div>
                    </div>

                    {{-- Indeks Pertanaman --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Indeks Pertanaman (IP)</p>
                            <p class="text-3xl font-bold text-green-700 mt-1">
                                {{ number_format($ip->ip, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Total Luas Tanam --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Total Luas Tanam</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">
                                {{ number_format($ip->total_luas_tanam, 0, ',', '.') }}
                                <span class="text-sm text-gray-600">Ha</span>
                            </p>
                        </div>
                    </div>

                    {{-- Luas Baku Sawah --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Luas Baku Sawah (LBS)</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">
                                {{ number_format($ip->luas_baku_sawah, 2, ',', '.') }}
                                <span class="text-sm text-gray-600">Ha</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Referensi: Tahun {{ $ip->lbs_tahun_referensi }}</p>
                        </div>
                    </div>

                    {{-- Created At --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Dibuat</p>
                            <p class="text-sm font-bold text-gray-900 mt-1">{{ $ip->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $ip->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Updated At --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Terakhir Diubah</p>
                            <p class="text-sm font-bold text-gray-900 mt-1">{{ $ip->updated_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $ip->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN - Calculation & Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Calculation Formula Card --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Rumus Perhitungan
                    </h2>
                </div>

                <div class="p-6">
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-green-900 mb-4">Formula Indeks Pertanaman:</h3>
                        <div class="flex flex-col items-center space-y-4">
                            <div class="text-center">
                                <div class="inline-block bg-white border-2 border-green-500 rounded-lg px-6 py-3">
                                    <p class="text-sm text-gray-600 mb-1">Total Luas Tanam</p>
                                    <p class="text-2xl font-bold text-green-700">{{ number_format($ip->total_luas_tanam, 0, ',', '.') }} Ha</p>
                                </div>
                            </div>
                            
                            <div class="text-4xl font-bold text-green-600">÷</div>
                            
                            <div class="text-center">
                                <div class="inline-block bg-white border-2 border-green-500 rounded-lg px-6 py-3">
                                    <p class="text-sm text-gray-600 mb-1">Luas Baku Sawah (LBS {{ $ip->lbs_tahun_referensi }})</p>
                                    <p class="text-2xl font-bold text-teal-700">{{ number_format($ip->luas_baku_sawah, 2, ',', '.') }} Ha</p>
                                </div>
                            </div>
                            
                            <div class="text-4xl font-bold text-green-600">=</div>
                            
                            <div class="text-center">
                                <div class="inline-block bg-gradient-to-br from-green-500 to-green-700 rounded-lg px-8 py-4 shadow-lg">
                                    <p class="text-sm text-white/90 mb-1">Indeks Pertanaman (IP)</p>
                                    <p class="text-4xl font-bold text-white">{{ number_format($ip->ip, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan Card --}}
            @if($ip->keterangan)
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Keterangan
                    </h2>
                </div>

                <div class="p-6">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <p class="text-gray-700">{{ $ip->keterangan }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Information Box --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-6 rounded-r-lg shadow-md">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-green-900 mb-2">Informasi Indeks Pertanaman (IP)</h3>
                        <p class="text-sm text-green-800 mb-3">
                            Indeks Pertanaman (IP) adalah angka yang menunjukkan intensitas penanaman tanaman padi dalam setahun pada suatu wilayah. IP dihitung dengan membagi total luas tanam dengan Luas Baku Sawah (LBS).
                        </p>
                        <div class="bg-green-100 p-3 rounded-lg space-y-2">
                            <p class="text-xs text-green-800 font-semibold">Interpretasi IP:</p>
                            <ul class="text-xs text-green-700 space-y-1">
                                <li>• IP = 1.00 : Satu kali tanam dalam setahun</li>
                                <li>• IP = 2.00 : Dua kali tanam dalam setahun</li>
                                <li>• IP = 3.00 : Tiga kali tanam dalam setahun</li>
                                <li>• IP > 3.00 : Sangat intensif (lebih dari 3x tanam)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    

</div>
@endsection