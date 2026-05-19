<div class="flex h-16 shrink-0 items-center">
    <h1 class="text-xl font-bold text-primary-600">
        🌾 Rekap Padi Sumsel
    </h1>
</div>

<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                              {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- ═══ LTT DAN LTP ═══ -->
                @php
                    $lttActive   = request()->routeIs('rekap.bulanan.tanam.*') || request()->routeIs('rekap.tahunan.tanam.*')
                                || request()->routeIs('rekap.harian.tanam.*')
                                || request()->routeIs('rekap.bulanan.panen.*') || request()->routeIs('rekap.tahunan.panen.*')
                                || request()->routeIs('rekap.harian.panen.*');
                    $tanamActive = request()->routeIs('rekap.bulanan.tanam.*') || request()->routeIs('rekap.tahunan.tanam.*')
                                || request()->routeIs('rekap.harian.tanam.*');
                    $panenActive = request()->routeIs('rekap.bulanan.panen.*') || request()->routeIs('rekap.tahunan.panen.*')
                                || request()->routeIs('rekap.harian.panen.*');
                @endphp

                <li x-data="{
                        ltt:   {{ $lttActive   ? 'true' : 'false' }},
                        tanam: {{ $tanamActive ? 'true' : 'false' }},
                        panen: {{ $panenActive ? 'true' : 'false' }}
                    }">
                    <button @click="ltt = !ltt"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ $lttActive ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                            </svg>
                            LTT dan LTP
                        </div>
                        <svg :class="{'rotate-90': ltt}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="ltt" x-cloak class="mt-1 ml-2 pl-3 border-l-2 border-gray-100">

                        <!-- Sub: Luas Tanam -->
                        <div>
                            <button @click="tanam = !tanam"
                                    class="w-full flex items-center justify-between gap-x-2 rounded-md px-2 py-1.5 text-sm font-semibold
                                           {{ $tanamActive ? 'text-primary-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-x-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded text-xs font-bold
                                                 {{ $tanamActive ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }}">T</span>
                                    Luas Tanam
                                </span>
                                <svg :class="{'rotate-90': tanam}" class="h-4 w-4 text-gray-400 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <ul x-show="tanam" x-cloak class="mt-1 px-2 space-y-0.5">
                                <li>
                                    <a href="{{ route('rekap.bulanan.tanam.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.bulanan.tanam.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Bulanan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('rekap.tahunan.tanam.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.tahunan.tanam.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Tahunan
                                    </a>
                                </li>
                                @if(Auth::user()->canManageData())
                                <li>
                                    <a href="{{ route('rekap.harian.tanam.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.harian.tanam.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Harian
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <div class="my-1 border-t border-gray-100"></div>

                        <!-- Sub: Luas Panen -->
                        <div>
                            <button @click="panen = !panen"
                                    class="w-full flex items-center justify-between gap-x-2 rounded-md px-2 py-1.5 text-sm font-semibold
                                           {{ $panenActive ? 'text-primary-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-x-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded text-xs font-bold
                                                 {{ $panenActive ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }}">P</span>
                                    Luas Panen
                                </span>
                                <svg :class="{'rotate-90': panen}" class="h-4 w-4 text-gray-400 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <ul x-show="panen" x-cloak class="mt-1 px-2 space-y-0.5">
                                <li>
                                    <a href="{{ route('rekap.bulanan.panen.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.bulanan.panen.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Bulanan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('rekap.tahunan.panen.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.tahunan.panen.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Tahunan
                                    </a>
                                </li>
                                @if(Auth::user()->canManageData())
                                <li>
                                    <a href="{{ route('rekap.harian.panen.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('rekap.harian.panen.*') ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Rekap Harian
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- ═══ KSA ═══ -->
                @php
                    $ksaActive         = request()->routeIs('ksa.tanam.*') || request()->routeIs('ksa.panen.*') || request()->routeIs('ksa.produksi.*');
                    $ksaTanamActive    = request()->routeIs('ksa.tanam.*');
                    $ksaPanenActive    = request()->routeIs('ksa.panen.*');
                    $ksaProduksiActive = request()->routeIs('ksa.produksi.*');
                @endphp
                <li x-data="{
                        ksa:         {{ $ksaActive         ? 'true' : 'false' }},
                        ksaTanam:    {{ $ksaTanamActive    ? 'true' : 'false' }},
                        ksaPanen:    {{ $ksaPanenActive    ? 'true' : 'false' }},
                        ksaProduksi: {{ $ksaProduksiActive ? 'true' : 'false' }}
                    }">
                    <button @click="ksa = !ksa"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ $ksaActive ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            KSA
                        </div>
                        <svg :class="{'rotate-90': ksa}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="ksa" x-cloak class="mt-1 ml-2 pl-3 border-l-2 border-gray-100">

                        <!-- KSA Luas Tanam -->
                        <div>
                            <button @click="ksaTanam = !ksaTanam"
                                    class="w-full flex items-center justify-between gap-x-2 rounded-md px-2 py-1.5 text-sm font-semibold
                                           {{ $ksaTanamActive ? 'text-primary-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-x-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded text-xs font-bold
                                                 {{ $ksaTanamActive ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">T</span>
                                    Luas Tanam
                                </span>
                                <svg :class="{'rotate-90': ksaTanam}" class="h-4 w-4 text-gray-400 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <ul x-show="ksaTanam" x-cloak class="mt-1 px-2 space-y-0.5">
                                <li>
                                    <a href="{{ route('ksa.tanam.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.tanam.index') || request()->routeIs('ksa.tanam.show') || request()->routeIs('ksa.tanam.create') || request()->routeIs('ksa.tanam.edit') ? 'bg-yellow-50 text-yellow-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Tahunan per Bulan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.tanam.bulanan.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.tanam.bulanan.*') ? 'bg-yellow-50 text-yellow-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Bulanan per Tahun
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.tanam.total.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.tanam.total.*') ? 'bg-yellow-50 text-yellow-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Total Tahunan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="my-1 border-t border-gray-100"></div>

                        <!-- KSA Luas Panen -->
                        <div>
                            <button @click="ksaPanen = !ksaPanen"
                                    class="w-full flex items-center justify-between gap-x-2 rounded-md px-2 py-1.5 text-sm font-semibold
                                           {{ $ksaPanenActive ? 'text-primary-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-x-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded text-xs font-bold
                                                 {{ $ksaPanenActive ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500' }}">P</span>
                                    Luas Panen
                                </span>
                                <svg :class="{'rotate-90': ksaPanen}" class="h-4 w-4 text-gray-400 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <ul x-show="ksaPanen" x-cloak class="mt-1 px-2 space-y-0.5">
                                <li>
                                    <a href="{{ route('ksa.panen.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.panen.index') || request()->routeIs('ksa.panen.show') || request()->routeIs('ksa.panen.create') || request()->routeIs('ksa.panen.edit') ? 'bg-violet-50 text-violet-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Tahunan per Bulan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.panen.bulanan.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.panen.bulanan.*') ? 'bg-violet-50 text-violet-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Bulanan per Tahun
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.panen.total.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.panen.total.*') ? 'bg-violet-50 text-violet-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Total Tahunan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="my-1 border-t border-gray-100"></div>

                        <!-- KSA Produksi -->
                        <div>
                            <button @click="ksaProduksi = !ksaProduksi"
                                    class="w-full flex items-center justify-between gap-x-2 rounded-md px-2 py-1.5 text-sm font-semibold
                                           {{ $ksaProduksiActive ? 'text-primary-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-x-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded text-xs font-bold
                                                 {{ $ksaProduksiActive ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">Pr</span>
                                    Produksi
                                </span>
                                <svg :class="{'rotate-90': ksaProduksi}" class="h-4 w-4 text-gray-400 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <ul x-show="ksaProduksi" x-cloak class="mt-1 px-2 space-y-0.5">
                                <li>
                                    <a href="{{ route('ksa.produksi.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.produksi.index') || request()->routeIs('ksa.produksi.show') || request()->routeIs('ksa.produksi.create') || request()->routeIs('ksa.produksi.edit') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Tahunan per Bulan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.produksi.bulanan.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.produksi.bulanan.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Bulanan per Tahun
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ksa.produksi.total.index') }}"
                                       class="block rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                              {{ request()->routeIs('ksa.produksi.total.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sanding Total Tahunan
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </li>

                <!-- ═══ INDEKS PERTANAMAN (IP) ═══ -->
                <li x-data="{ open: {{ request()->routeIs('ip.padi.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ request()->routeIs('ip.padi.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                            </svg>
                            Indeks Pertanaman (IP)
                        </div>
                        <svg :class="{'rotate-90': open}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 px-2 space-y-0.5">
                        <li>
                            <a href="{{ route('ip.padi.index') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('ip.padi.*') ? 'text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                IP Tanaman Padi
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ═══ SANDING LBS ═══ -->
                <li x-data="{ open: {{ request()->routeIs('lbs.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ request()->routeIs('lbs.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                            </svg>
                            Sanding LBS
                        </div>
                        <svg :class="{'rotate-90': open}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 px-2">
                        <li>
                            <a href="{{ route('lbs.index') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('lbs.index') ? 'text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                Sanding Luas Baku Sawah
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ═══ GRAFIK ═══ -->
                @php
                    $grafikActive = request()->routeIs('grafik.*');
                @endphp
                <li x-data="{ open: {{ $grafikActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ $grafikActive ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                            Grafik
                        </div>
                        <svg :class="{'rotate-90': open}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 px-2 space-y-0.5">
                        {{-- LTT --}}
                        <li class="pt-1 pb-0.5 px-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">LTT &amp; LTP</p>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ltt.tanam') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ltt.tanam') ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik Luas Tanam
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ltt.panen') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ltt.panen') ? 'bg-violet-50 text-violet-600 font-semibold' : 'text-gray-700 hover:bg-violet-50 hover:text-violet-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik Luas Panen
                            </a>
                        </li>
                        {{-- KSA --}}
                        <li class="pt-2 pb-0.5 px-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">KSA</p>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ksa.tanam') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ksa.tanam') ? 'bg-amber-50 text-amber-600 font-semibold' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik KSA Luas Tanam
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ksa.panen') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ksa.panen') ? 'bg-violet-50 text-violet-600 font-semibold' : 'text-gray-700 hover:bg-violet-50 hover:text-violet-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik KSA Luas Panen
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ksa.produksi') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ksa.produksi') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik KSA Produksi
                            </a>
                        </li>
                        {{-- IP & LBS --}}
                        <li class="pt-2 pb-0.5 px-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">IP &amp; LBS</p>
                        </li>
                        <li>
                            <a href="{{ route('grafik.ip.padi') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.ip.padi') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-gray-700 hover:bg-emerald-50 hover:text-emerald-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik IP Padi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('grafik.sanding.lbs') }}"
                               class="flex items-center gap-x-1.5 rounded-md py-2 pl-7 pr-2 text-sm leading-6
                                      {{ request()->routeIs('grafik.sanding.lbs') ? 'bg-teal-50 text-teal-600 font-semibold' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-600' }}">
                                <svg class="h-3.5 w-3.5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Grafik Sanding LBS
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ═══ DATA MASTER ═══ -->
                @if(Auth::user()->role->name === 'master_admin')
                <li x-data="{ open: {{ request()->routeIs('admin.kabupaten.*') || request()->routeIs('admin.kecamatan.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ request()->routeIs('admin.kabupaten.*') || request()->routeIs('admin.kecamatan.*') ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
                            </svg>
                            Data Master
                        </div>
                        <svg :class="{'rotate-90': open}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 px-2">
                        <li>
                            <a href="{{ route('admin.kabupaten.index') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('admin.kabupaten.*') ? 'text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                Kabupaten
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.kecamatan.index') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('admin.kecamatan.*') ? 'text-primary-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                Kecamatan
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- ═══ MANAJEMEN ADMIN ═══ -->
                @if(Auth::user()->role->name === 'master_admin')
                <li x-data="{ open: {{ request()->routeIs('admin.admins.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between gap-x-3 rounded-md p-2 text-sm font-semibold leading-6
                                   {{ request()->routeIs('admin.admins.*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50 hover:text-red-600' }}">
                        <div class="flex items-center gap-x-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                            Manajemen Admin
                        </div>
                        <svg :class="{'rotate-90': open}" class="h-5 w-5 shrink-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 px-2 space-y-0.5">
                        <li>
                            <a href="{{ route('admin.admins.index') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('admin.admins.index') || request()->routeIs('admin.admins.show') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                Daftar Admin
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.admins.create') }}"
                               class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6
                                      {{ request()->routeIs('admin.admins.create') ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                Tambah Admin Baru
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </li>

        <!-- Bottom section -->
        <li class="-mx-6 mt-auto">
            <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 border-t border-gray-200">
                <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center">
                    <span class="text-white font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <span class="block text-sm font-semibold">{{ Auth::user()->name }}</span>
                    <span class="block text-xs text-gray-500">{{ Auth::user()->role->display_name }}</span>
                </div>
            </div>
        </li>
    </ul>
</nav>