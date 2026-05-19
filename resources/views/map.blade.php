@extends('layouts.topbar.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map {
        position: fixed;
        top: 64px;
        left: 0; right: 0; bottom: 0;
        z-index: 1;
    }
    .kab-label {
        font-size: 10px;
        font-weight: 700;
        color: #1d3557;
        background: none   !important;
        border: none       !important;
        box-shadow: none   !important;
        text-shadow: 0 1px 3px rgba(255,255,255,0.9);
        font-family: 'Figtree', sans-serif;
    }
    .info-box {
        position: fixed;
        top: 84px;
        right: 20px;
        z-index: 999;
        max-width: 310px;
        min-width: 260px;
        display: none;
    }
    .hint {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(20,83,45,0.88);
        color: #fff;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.79rem;
        font-weight: 500;
        z-index: 999;
        pointer-events: none;
        transition: opacity 0.5s;
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }
    .legend {
        position: fixed;
        bottom: 28px;
        left: 20px;
        z-index: 999;
    }
    .map-loading {
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255,255,255,0.9);
        border-radius: 12px;
        padding: 20px 32px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        font-size: 0.85rem;
        color: #374151;
        font-weight: 500;
    }
    .spinner {
        width: 20px; height: 20px;
        border: 3px solid #e5e7eb;
        border-top-color: #16a34a;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 768px) {
        .legend { display: none; }
        .info-box { max-width: calc(100vw - 32px); right: 16px; }
    }
    body { overflow: hidden; }
</style>
@endpush

@section('content')

<div class="map-loading" id="mapLoading">
    <div class="spinner"></div>
    <span>Memuat data peta...</span>
</div>

<div id="map"></div>

{{-- ── FILTER PANEL ────────────────────────────────────────────────── --}}
<div class="fixed top-[84px] left-4 z-[400] bg-white/95 backdrop-blur rounded-xl shadow-lg border border-gray-200 p-4 w-72">
    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filter Peta
    </h3>
    
    <div class="space-y-3 text-sm">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Data</label>
            <select id="filterJenisData" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" onchange="updatePeriodeOptions()">
                <optgroup label="Monitoring Sawah">
                    <option value="ltt" selected>Luas Tanam (LTT)</option>
                    <option value="ltp">Luas Panen (LTP)</option>
                </optgroup>
                <optgroup label="Data KSA">
                    <option value="ksa_tanam">KSA Luas Tanam</option>
                    <option value="ksa_panen">KSA Luas Panen</option>
                    <option value="ksa_produksi">KSA Produksi</option>
                </optgroup>
                <optgroup label="Indikator Lain">
                    <option value="ip_padi">Indeks Pertanaman (IP Padi)</option>
                    <option value="lbs">Luas Baku Sawah (LBS)</option>
                </optgroup>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Periode Wilayah</label>
            <select id="filterPeriode" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" onchange="toggleFilterInputs()">
                <option value="harian">Harian (Level Kecamatan)</option>
                <option value="bulanan">Bulanan (Level Kabupaten)</option>
                <option value="tahunan" selected>Tahunan (Level Kabupaten)</option>
            </select>
        </div>

        <div id="wrapTanggal" style="display:none;">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Harian</label>
            <select id="filterTanggal" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                <option value="{{ date('Y-m-d') }}">{{ date('Y-m-d') }}</option>
            </select>
        </div>

        <div id="wrapBulan" style="display:none;">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
            <select id="filterBulan" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                <option value="januari">Januari</option>
            </select>
        </div>

        <div id="wrapTahun">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun</label>
            <select id="filterTahun" class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
            </select>
        </div>

        <div class="pt-2">
            <button onclick="fetchDynamicData()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex justify-center items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Terapkan Parameter
            </button>
        </div>
    </div>
</div>

{{-- ── INFO BOX ────────────────────────────────────────────────── --}}
<div class="info-box" id="infoBox">
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 flex items-center justify-between" id="infoHeader">
            <div class="overflow-hidden">
                <p class="text-[10px] font-medium mb-0.5 text-white/70" id="infoSubtitle">—</p>
                <span class="text-sm font-bold text-white leading-tight block truncate" id="infoTitle">—</span>
            </div>
            <button onclick="document.getElementById('infoBox').style.display='none'"
                class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs hover:bg-white/20 transition-colors flex-shrink-0 ml-2">✕</button>
        </div>
        <div class="p-3" id="infoContent"></div>
    </div>
</div>

{{-- ── LEGENDA ─────────────────────────────────────────────────── --}}
<div class="legend">
    <div class="bg-white/95 backdrop-blur rounded-xl shadow-lg border border-gray-200 p-3 text-xs text-gray-700" id="legendContent">
        <p class="font-bold text-gray-800 mb-2">Memuat Legenda...</p>
    </div>
</div>

<div class="hint" id="hint">🖱 Klik kabupaten untuk detail</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── URL RELATIF (aman dari CORS / ngrok / mixed-content) ─────────
var URL_API_KAB     = '/api/map/kabupaten-data';
var URL_API_KEC     = '/api/map/kecamatan-data';
var URL_KAB_GEOJSON = '/api/map/geojson/kabupaten';
// ── URL RELATIF (aman dari CORS) ─────────
var URL_API_DYNAMIC = '/api/map/dynamic-data';
var URL_API_FILTERS = '/api/map/available-filters';
var URL_KAB_GEOJSON = '/api/map/geojson/kabupaten';
var URL_KEC_GEOJSON = '/api/map/geojson/kecamatan';

function apiFetch(url) {
    return fetch(url, {
        headers: {
            'ngrok-skip-browser-warning': 'true',
            'X-Requested-With'          : 'XMLHttpRequest',
            'Accept'                    : 'application/json, application/geo+json, */*',
        }
    }).then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status + ' ' + r.statusText);
        var ct = r.headers.get('content-type') || '';
        if (ct.includes('text/html')) throw new Error('Response HTML – cek ngrok / session login');
        return r.json();
    });
}

function fetchAvailableFilters() {
    var jenis = document.getElementById('filterJenisData').value;
    var periode = document.getElementById('filterPeriode').value;
    var url = URL_API_FILTERS + '?jenis_data=' + jenis + '&periode=' + periode;

    apiFetch(url).then(function(res) {
        var elTahun = document.getElementById('filterTahun');
        var elBulan = document.getElementById('filterBulan');
        var elTanggal = document.getElementById('filterTanggal');
        
        // Populate Tahun
        elTahun.innerHTML = '';
        if(res.years && res.years.length > 0) {
            res.years.forEach(function(y) {
                elTahun.innerHTML += '<option value="'+y+'">'+y+'</option>';
            });
        } else {
            elTahun.innerHTML = '<option value="'+new Date().getFullYear()+'">Belum Ada Data</option>';
        }

        // Populate Bulan
        elBulan.innerHTML = '';
        if(res.months && res.months.length > 0) {
            res.months.forEach(function(m) {
                var cap = m.charAt(0).toUpperCase() + m.slice(1);
                elBulan.innerHTML += '<option value="'+m+'">'+cap+'</option>';
            });
        } else {
            elBulan.innerHTML = '<option value="januari">Belum Ada Data</option>';
        }

        // Populate Tanggal
        elTanggal.innerHTML = '';
        if(res.dates && res.dates.length > 0) {
            res.dates.forEach(function(d) {
                elTanggal.innerHTML += '<option value="'+d+'">'+d+'</option>';
            });
        } else {
             var today = new Date().toISOString().split('T')[0];
             elTanggal.innerHTML = '<option value="'+today+'">Belum Ada Data</option>';
        }
    }).catch(function(err) {
        console.error("Gagal load filters: ", err);
    });
}

function updatePeriodeOptions() {
    var jenis = document.getElementById('filterJenisData').value;
    var periode = document.getElementById('filterPeriode');
    var isHarianEligible = ['ltt', 'ltp'].includes(jenis);
    
    // Tampilkan/sembunyikan opsi Harian
    Array.from(periode.options).forEach(opt => {
        if (opt.value === 'harian') opt.style.display = isHarianEligible ? '' : 'none';
    });

    if (periode.value === 'harian' && !isHarianEligible) {
        periode.value = 'tahunan';
    }
    toggleFilterInputs();
}

function toggleFilterInputs() {
    var p = document.getElementById('filterPeriode').value;
    document.getElementById('wrapTanggal').style.display = p === 'harian' ? 'block' : 'none';
    document.getElementById('wrapBulan').style.display = p === 'bulanan' ? 'block' : 'none';
    document.getElementById('wrapTahun').style.display = ['bulanan', 'tahunan'].includes(p) ? 'block' : 'none';
    
    // Fetch limits for available times based on active filter combination
    fetchAvailableFilters();
}
var map = L.map('map').setView([-3.3194, 103.914], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
    maxZoom: 18,
}).addTo(map);

map.zoomControl.setPosition('bottomright');

var hint         = document.getElementById('hint');
var infoBox      = document.getElementById('infoBox');
var infoHeader   = document.getElementById('infoHeader');
var infoTitle    = document.getElementById('infoTitle');
var infoSubtitle = document.getElementById('infoSubtitle');
var infoContent  = document.getElementById('infoContent');
var mapLoading   = document.getElementById('mapLoading');

var geojsonKab = null;
var geojsonKec = null;
var currentMapData = {};
var currentMapLevel = 'kabupaten'; // 'kabupaten' | 'kecamatan'
var maxValue = 0;
var kabLayer, kecLayer;
// ═══════════════════════════════════════════════════════════════
// STYLES DYNAMIC CHOROPLETH (DYNAMIC QUINTILES)
// ═══════════════════════════════════════════════════════════════
function getColor(value, max) {
    if (!value || value === 0) return '#e5e7eb'; // Abu-abu jika kosong
    if (!max || max === 0) return '#addd8e';
    var pct = value / max;
    if (pct >= 0.8) return '#005a32'; // Sangat gelap
    if (pct >= 0.6) return '#238b45'; // Gelap
    if (pct >= 0.4) return '#41ab5d'; // Menengah
    if (pct >= 0.2) return '#addd8e'; // Terang
    return '#f7fcb9'; // Sangat terang ( < 20% )
}

function renderLegend() {
    var lbl = document.getElementById('filterJenisData');
    var labelMetric = lbl.options[lbl.selectedIndex].text;
    
    var unit = (labelMetric.toLowerCase().includes('produksi')) ? 'Ton' : (labelMetric.toLowerCase().includes('indeks') ? '' : 'Ha');
    
    var q1 = 0, q2 = 0, q3 = 0, q4 = 0;
    if (maxValue > 0) {
        q1 = maxValue * 0.2;
        q2 = maxValue * 0.4;
        q3 = maxValue * 0.6;
        q4 = maxValue * 0.8;
    }

    function fm(v) {
        if (!v) return '0';
        return Number(v).toLocaleString('id-ID', { maximumFractionDigits: 1 });
    }
    
    var ranges = [
        { c: '#005a32', t: '> ' + fm(q4) + ' ' + unit },
        { c: '#238b45', t: fm(q3) + ' - ' + fm(q4) + ' ' + unit },
        { c: '#41ab5d', t: fm(q2) + ' - ' + fm(q3) + ' ' + unit },
        { c: '#addd8e', t: fm(q1) + ' - ' + fm(q2) + ' ' + unit },
        { c: '#f7fcb9', t: '< ' + fm(q1) + ' ' + unit },
        { c: '#e5e7eb', t: 'Belum Ada Data (0)' }
    ];

    var html = '<p class="font-bold text-gray-800 mb-1 border-b pb-1 border-gray-100">Sebaran Data Dinamis</p>';
    if (labelMetric.length > 25) {
        html += '<p class="text-[10px] text-gray-500 mb-2 truncate max-w-[150px]" title="'+labelMetric+'">'+labelMetric+'</p>';
    } else {
        html += '<p class="text-[10px] text-gray-500 mb-2">'+labelMetric+'</p>';
    }

    ranges.forEach(function(r) {
        html += '<div class="flex items-center gap-2 mb-1"><div class="w-4 h-4 rounded-sm shadow-sm border border-black/10" style="background:'+r.c+';"></div><span class="font-medium">'+r.t+'</span></div>';
    });

    if (currentMapLevel === 'kecamatan') {
        html += '<div class="mt-2 pt-2 border-t border-gray-200 flex items-center gap-2">';
        html += '<div class="w-4 h-0 border-t-2 border-slate-700 border-dashed"></div>';
        html += '<span class="text-[10px] italic">Batas Kabupaten</span></div>';
    }

    document.getElementById('legendContent').innerHTML = html;
}

function getStyleDynamic(feature, level) {
    var key = normalizeName(level === 'kecamatan' 
        ? (feature.properties.name || feature.properties.NAME_3 || '')
        : (feature.properties.name || feature.properties.NAME_2 || feature.properties.NAMOBJ || ''));
    
    var val = 0;
    var dataGeo = getDataByKey(feature.properties.name || feature.properties.NAME_3 || feature.properties.NAME_2 || feature.properties.NAMOBJ || '');
    if (dataGeo) {
        val = dataGeo.value;
    }

    return {
        fillColor: getColor(val, maxValue),
        weight: level === 'kabupaten' ? 1.5 : 1,
        color: level === 'kabupaten' ? '#ffffff' : '#f3f4f6', // Bright white/light grid
        fillOpacity: val > 0 ? 0.85 : 0.45
    };
}

function getStyleDynamicHover(feature, level) {
    var base = getStyleDynamic(feature, level);
    base.fillColor = '#f59e0b'; // Hover orange
    base.fillOpacity = 0.9;
    return base;
}

function normalizeName(str) {
    return (str || '').toUpperCase().trim()
        .replace(/\s+/g, ' ')
        .replace(/\bKABUPATEN\s+/gi, '')
        .replace(/\bKAB\.?\s+/gi, '')
        .replace(/\bKEC\.?\s*/gi, '')
        .replace(/\bKECAMATAN\s*/gi, '')
        .replace(/\bKOTA\s+/gi, '')
        .trim();
}

function getDataByKey(nameGeoJson) {
    var key = normalizeName(nameGeoJson);
    if (currentMapData[key]) return currentMapData[key];
    
    // Explicit Aliases to prevent mismatches
    var aliases = {
        "PALI": "PENUKAL ABAB LEMATANG ILIR",
        "OKU": "OGAN KOMERING ULU",
        "OKU TIMUR": "OGAN KOMERING ULU TIMUR",
        "OKU SELATAN": "OGAN KOMERING ULU SELATAN",
        "OKI": "OGAN KOMERING ILIR"
    };

    // If key matches alias
    if (aliases[key] && currentMapData[aliases[key]]) {
        return currentMapData[aliases[key]];
    }

    // Reverse lookup
    for(var k in aliases) {
        if (aliases[k] === key && currentMapData[k]) {
            return currentMapData[k];
        }
    }

    // Hard fallback: return null explicitly if no exact match (previously used .includes which caused Musi Rawas vs Musi Rawas Utara map bug)
    return null;
}

// ═══════════════════════════════════════════════════════════════
// TEMPLATE
// ═══════════════════════════════════════════════════════════════
function fmt(val, satuan) {
    satuan = satuan || '';
    if (!val || val === 0) return '<span class="text-gray-300 font-normal">—</span>';
    var n = Number(val).toLocaleString('id-ID', { maximumFractionDigits: 2 });
    return '<span class="font-bold text-gray-900">' + n + '</span>'
         + (satuan ? '<span class="text-[10px] text-gray-400"> ' + satuan + '</span>' : '');
}

function showInfoDynamic(feature, level, codeProp) {
    var isKec = level === 'kecamatan';
    var nameProp = isKec 
        ? (feature.properties.name || feature.properties.NAME_3 || '')
        : (feature.properties.name || feature.properties.NAME_2 || feature.properties.NAMOBJ || '');
    
    var namaGeo = nameProp || '-';
    var dataGeo = getDataByKey(namaGeo);
    
    var j = document.getElementById('filterJenisData');
    var labelMetric = j.options[j.selectedIndex].text;
    var p = document.getElementById('filterPeriode');
    var labelPeriode = p.options[p.selectedIndex].text;

    if (j.value === 'ltt' || j.value === 'ltp') {
        var baseType = j.value.toUpperCase(); // LTT or LTP
        if (p.value === 'harian') {
            labelMetric = 'Total ' + baseType + ' Reguler';
        } else if (p.value === 'tahunan' || p.value === 'bulanan') {
            labelMetric = 'Total ' + baseType;
        }
    }

    var val = dataGeo ? dataGeo.value : 0;
    var tgl = dataGeo ? (dataGeo.tanggal || dataGeo.tahun || '') : document.getElementById('filterTahun').value;
    
    var u = (labelMetric.toLowerCase().includes('produksi')) ? 'Ton GKG' : (labelMetric.toLowerCase().includes('indeks') ? '' : 'Ha');

    infoHeader.style.background = isKec ? 'linear-gradient(135deg, #14532d, #16a34a)' : 'linear-gradient(135deg, #1e3a5f, #2563eb)';
    infoSubtitle.textContent    = isKec ? (dataGeo ? dataGeo.nama_kabupaten : 'Tingkat Kecamatan') : 'Tingkat Kabupaten/Kota';
    infoTitle.textContent       = namaGeo;

    var grid = '<div class="rounded-lg p-4 text-center border border-green-100 shadow-inner" style="background:#f0fdf4;">'
        +   '<p class="text-[10px] font-bold uppercase tracking-wider text-green-700 mb-1">' + labelMetric + '</p>'
        +   '<p class="text-xl leading-tight">' + fmt(val, u) + '</p>';
        
    if (dataGeo && dataGeo.reguler !== undefined) {
        grid += '<div class="mt-3 grid grid-cols-2 gap-2 text-left bg-white/60 p-2 rounded-md">'
             +  '<div class="text-[9px] text-gray-600">Reguler:<br><span class="font-bold text-gray-800">' + fmt(dataGeo.reguler, u) + '</span></div>'
             +  '<div class="text-[9px] text-gray-600">Oplah:<br><span class="font-bold text-gray-800">' + fmt(dataGeo.oplah, u) + '</span></div>'
             +  '<div class="text-[9px] text-gray-600">Gogo:<br><span class="font-bold text-gray-800">' + fmt(dataGeo.gogo, u) + '</span></div>'
             +  '<div class="text-[9px] text-gray-600">CSR:<br><span class="font-bold text-gray-800">' + fmt(dataGeo.csr, u) + '</span></div>'
             +  '</div>';
    }

    grid += '<p class="text-[9px] text-green-600 mt-2 font-medium bg-green-200/50 inline-block px-2 py-0.5 rounded-full">' + labelPeriode + (tgl ? ' · ' + tgl : '') + '</p>'
        + '</div>';

    infoContent.innerHTML = grid;
    infoBox.style.display = 'block';
}

function setHint(t) { hint.textContent = t; hint.style.opacity = '1'; }
function hideHint()  { hint.style.opacity = '0'; }

// ═══════════════════════════════════════════════════════════════
// FETCH API DINAMIS
// ═══════════════════════════════════════════════════════════════
function fetchDynamicData() {
    mapLoading.style.display = 'flex';
    infoBox.style.display = 'none';

    var jd = document.getElementById('filterJenisData').value;
    var per = document.getElementById('filterPeriode').value;
    var tgl = document.getElementById('filterTanggal').value;
    var bln = document.getElementById('filterBulan').value;
    var thn = document.getElementById('filterTahun').value;

    var url = URL_API_DYNAMIC + '?jenis_data=' + jd + '&periode=' + per 
            + '&tanggal=' + tgl + '&bulan=' + bln + '&tahun=' + thn;

    apiFetch(url).then(function(res) {
        currentMapLevel = res.level;
        
        // Re-index data by Normalized GeoJSON Name so mapping works out of the box!
        currentMapData = {};
        maxValue = 0;
        for (var id in res.data) {
            var o = res.data[id];
            // If data is kecamatan, it has nama_kecamatan. If Kabupaten, it has nama_kabupaten
            var keyNM = normalizeName(o.nama_kecamatan || o.nama_kabupaten || '');
            currentMapData[keyNM] = o;
            
            // Cari maxValue dinamis per kueri
            if (o.value > maxValue) {
                maxValue = o.value;
            }
        }
        
        renderMap();
        
        // Tampilkan Legenda
        renderLegend();
        
        mapLoading.style.display = 'none';
        setHint('🖱 Peta berhasil diperbarui. Klik area untuk detail.');
        setTimeout(hideHint, 3000);
    }).catch(function(err) {
        console.error("Gagal load API MVC:", err);
        mapLoading.style.display = 'none';
        alert('Terjadi kesalahan memuat data. Silakan coba lagi.');
    });
}

// ═══════════════════════════════════════════════════════════════
// BOOT
// ═══════════════════════════════════════════════════════════════
mapLoading.style.display = 'flex';
Promise.all([
    apiFetch(URL_KEC_GEOJSON).catch(function() { return null; }),
    apiFetch(URL_KAB_GEOJSON).catch(function() { return null; })
]).then(function(results) {
    geojsonKec = results[0];
    geojsonKab = results[1];
    
    updatePeriodeOptions();
    // Wait slightly so that filters have fully populated before fetching map data
    setTimeout(fetchDynamicData, 600);
});

// ═══════════════════════════════════════════════════════════════
// RENDER MAP DINAMIS
// ═══════════════════════════════════════════════════════════════
function renderMap() {
    if (kabLayer) { map.removeLayer(kabLayer); kabLayer = null; }
    if (kecLayer) { map.removeLayer(kecLayer); kecLayer = null; }

    var isKec = (currentMapLevel === 'kecamatan');
    var geoData = isKec ? geojsonKec : geojsonKab;
    
    if (!geoData || !geoData.features) return;

    var sample   = geoData.features[0].properties;
    var nameProp = isKec 
                 ? (sample.name ? 'name' : (sample.NAME_3 ? 'NAME_3' : 'NAMOBJ'))
                 : (sample.name ? 'name' : (sample.NAME_2 ? 'NAME_2' : 'NAMOBJ'));

    var codeProp = isKec 
                 ? (sample.code ? 'code' : (sample.ADM3_PCODE ? 'ADM3_PCODE' : null))
                 : (sample.code ? 'code' : (sample.ADM2_PCODE ? 'ADM2_PCODE' : null));

    var activeLayer = L.geoJSON(geoData, {
        style: function(f) { return getStyleDynamic(f, currentMapLevel); },
        onEachFeature: function(f, l) {
            var nama = f.properties[nameProp] || '-';
            
            // Tooltip untuk Kecamatan / Kabupaten
            l.bindTooltip(nama, { permanent: !isKec, direction: 'center', className: 'kab-label bg-transparent border-0 shadow-none text-gray-800 text-shadow-sm font-bold text-[10px]' });
            
            l.on('mouseover', function() { l.setStyle(getStyleDynamicHover(f, currentMapLevel)); });
            l.on('mouseout',  function() { activeLayer.resetStyle(l); });
            l.on('click', function(e) {
                L.DomEvent.stopPropagation(e);
                showInfoDynamic(f, currentMapLevel, codeProp ? f.properties[codeProp] : null);
            });
        }
    }).addTo(map);

    if (isKec) {
        kecLayer = activeLayer;
        // Bikin overlay garis batas kabupaten di atas kecamatan
        kabLayer = L.geoJSON(geojsonKab, {
            style: {
                fillColor: 'transparent',
                fillOpacity: 0,
                color: '#334155', // Garis batas Slate gelap
                weight: 2.5,
                dashArray: '5, 5',
                opacity: 0.95
            },
            interactive: false,
            onEachFeature: function(f, l) {
                var namaKab = f.properties.name || f.properties.NAME_2 || f.properties.NAMOBJ || '-';
                // Permanently tampilkan penamaan Kabupaten dengan tulisan besar dan transparan
                l.bindTooltip(namaKab, { 
                    permanent: true, 
                    direction: 'center', 
                    className: 'bg-transparent border-0 shadow-none text-slate-800 opacity-60 text-shadow-md font-extrabold text-xs uppercase tracking-widest' 
                });
            }
        }).addTo(map);
    } else {
        kabLayer = activeLayer;
    }

    // Menyesuaikan zoom ke seluruh area
    map.fitBounds(activeLayer.getBounds());
}

map.on('click', function() { infoBox.style.display = 'none'; });
</script>
@endpush