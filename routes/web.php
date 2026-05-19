<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\RekapBulananTanamController;
use App\Http\Controllers\RekapBulananPanenController;
use App\Http\Controllers\RekapBulananProduksiController;
use App\Http\Controllers\RekapHarianTanamController;
use App\Http\Controllers\RekapHarianPanenController;
use App\Http\Controllers\RekapTahunanTanamController;
use App\Http\Controllers\RekapTahunanPanenController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\KecamatanController;

// KSA
use App\Http\Controllers\KsaTanamController;
use App\Http\Controllers\KsaTanamBulananController;
use App\Http\Controllers\KsaTanamTotalController;
use App\Http\Controllers\KsaPanenController;
use App\Http\Controllers\KsaPanenBulananController;
use App\Http\Controllers\KsaPanenTotalController;
use App\Http\Controllers\KsaProduksiController;
use App\Http\Controllers\KsaProduksiBulananController;
use App\Http\Controllers\KsaProduksiTotalController;

use App\Http\Controllers\IpPadiController;
use App\Http\Controllers\LbsController;

// Grafik controllers (admin)
use App\Http\Controllers\GrafikLttTanamController;
use App\Http\Controllers\GrafikLttPanenController;
use App\Http\Controllers\GrafikKsaTanamController;
use App\Http\Controllers\GrafikKsaPanenController;
use App\Http\Controllers\GrafikKsaProduksiController;
use App\Http\Controllers\GrafikIpPadiController;
use App\Http\Controllers\GrafikLbsController;

// USER CONTROLLERS
use App\Http\Controllers\User\DashboardController            as UserDashboardController;
use App\Http\Controllers\User\RekapBulananTanamController    as UserRekapBulananTanamController;
use App\Http\Controllers\User\RekapBulananPanenController    as UserRekapBulananPanenController;
use App\Http\Controllers\User\RekapTahunanTanamController    as UserRekapTahunanTanamController;
use App\Http\Controllers\User\RekapTahunanPanenController    as UserRekapTahunanPanenController;
use App\Http\Controllers\User\RekapHarianTanamController     as UserRekapHarianTanamController;
use App\Http\Controllers\User\RekapHarianPanenController     as UserRekapHarianPanenController;
use App\Http\Controllers\User\KsaTanamController             as UserKsaTanamController;
use App\Http\Controllers\User\KsaPanenController             as UserKsaPanenController;
use App\Http\Controllers\User\KsaProduksiController          as UserKsaProduksiController;
use App\Http\Controllers\User\KsaTanamBulananController      as UserKsaTanamBulananController;
use App\Http\Controllers\User\KsaPanenBulananController      as UserKsaPanenBulananController;
use App\Http\Controllers\User\KsaProduksiBulananController   as UserKsaProduksiBulananController;
use App\Http\Controllers\User\KsaTanamTotalController        as UserKsaTanamTotalController;
use App\Http\Controllers\User\KsaPanenTotalController        as UserKsaPanenTotalController;
use App\Http\Controllers\User\KsaProduksiTotalController     as UserKsaProduksiTotalController;
use App\Http\Controllers\User\IpPadiController               as UserIpPadiController;
use App\Http\Controllers\User\LbsController                  as UserLbsController;

// USER GRAFIK CONTROLLERS
use App\Http\Controllers\User\GrafikLttTanamController       as UserGrafikLttTanamController;
use App\Http\Controllers\User\GrafikLttPanenController       as UserGrafikLttPanenController;
use App\Http\Controllers\User\GrafikKsaTanamController       as UserGrafikKsaTanamController;
use App\Http\Controllers\User\GrafikKsaPanenController       as UserGrafikKsaPanenController;
use App\Http\Controllers\User\GrafikKsaProduksiController    as UserGrafikKsaProduksiController;
use App\Http\Controllers\User\GrafikIpPadiController         as UserGrafikIpPadiController;
use App\Http\Controllers\User\GrafikLbsController            as UserGrafikLbsController;

use App\Http\Controllers\MapSawahController;
use Illuminate\Support\Facades\Route;

// ── Peta ──────────────────────────────────────────────────────────────────────
Route::get('/map', [MapSawahController::class, 'index'])->name('map');
Route::get('/api/map/kecamatan-data',    [MapSawahController::class, 'kecamatanData'])->name('api.map.kecamatan');
Route::get('/api/map/kabupaten-data',    [MapSawahController::class, 'kabupatenData'])->name('api.map.kabupaten');
Route::get('/api/map/dynamic-data',      [MapSawahController::class, 'dynamicData'])->name('api.map.dynamic');
Route::get('/api/map/available-filters', [MapSawahController::class, 'availableFilters'])->name('api.map.available');
Route::get('/api/map/geojson/kabupaten', [MapSawahController::class, 'geojsonKabupaten'])->name('api.map.geojson.kabupaten');
Route::get('/api/map/geojson/kecamatan', [MapSawahController::class, 'geojsonKecamatan'])->name('api.map.geojson.kecamatan');

Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/ajax/get-kecamatan', [RekapHarianPanenController::class, 'getKecamatan'])->name('ajax.get.kecamatan');

// ============================================================
// USER ROUTES (publik — view only)
// ============================================================
Route::prefix('user')->name('user.')->group(function () {

    // ── Rekap Bulanan Tanam ──────────────────────────────────────────────────
    Route::get('rekap/bulanan/tanam/export',       [UserRekapBulananTanamController::class, 'export'])->name('rekap.bulanan.tanam.export');
    Route::get('rekap/bulanan/tanam',              [UserRekapBulananTanamController::class, 'index'])->name('rekap.bulanan.tanam.index');
    Route::get('rekap/bulanan/tanam/{id}',         [UserRekapBulananTanamController::class, 'show'])->name('rekap.bulanan.tanam.show');

    // ── Rekap Tahunan Tanam ──────────────────────────────────────────────────
    Route::get('rekap/tahunan/tanam/export',       [UserRekapTahunanTanamController::class, 'export'])->name('rekap.tahunan.tanam.export');
    Route::get('rekap/tahunan/tanam',              [UserRekapTahunanTanamController::class, 'index'])->name('rekap.tahunan.tanam.index');

    // ── Rekap Harian Tanam ───────────────────────────────────────────────────
    Route::get('rekap/harian/tanam/export', [UserRekapHarianTanamController::class, 'export'])->name('rekap.harian.tanam.export');
    Route::get('rekap/harian/tanam',        [UserRekapHarianTanamController::class, 'index'])->name('rekap.harian.tanam.index');

    // ── Rekap Bulanan Panen ──────────────────────────────────────────────────
    Route::get('rekap/bulanan/panen/export',       [UserRekapBulananPanenController::class, 'export'])->name('rekap.bulanan.panen.export');
    Route::get('rekap/bulanan/panen',              [UserRekapBulananPanenController::class, 'index'])->name('rekap.bulanan.panen.index');
    Route::get('rekap/bulanan/panen/{id}',         [UserRekapBulananPanenController::class, 'show'])->name('rekap.bulanan.panen.show');

    // ── Rekap Tahunan Panen ──────────────────────────────────────────────────
    Route::get('rekap/tahunan/panen/export',       [UserRekapTahunanPanenController::class, 'export'])->name('rekap.tahunan.panen.export');
    Route::get('rekap/tahunan/panen',              [UserRekapTahunanPanenController::class, 'index'])->name('rekap.tahunan.panen.index');

    // ── Rekap Harian Panen ───────────────────────────────────────────────────
    Route::get('rekap/harian/panen/export', [UserRekapHarianPanenController::class, 'export'])->name('rekap.harian.panen.export');
    Route::get('rekap/harian/panen',        [UserRekapHarianPanenController::class, 'index'])->name('rekap.harian.panen.index');


    // ── KSA ─────────────────────────────────────────────────────────────────
    Route::get('ksa/tanam/export',             [UserKsaTanamController::class, 'export'])->name('ksa.tanam.export');
    Route::get('ksa/tanam',                    [UserKsaTanamController::class, 'index'])->name('ksa.tanam.index');

    Route::get('ksa/tanam-bulanan/export',     [UserKsaTanamBulananController::class, 'export'])->name('ksa.tanam.bulanan.export');
    Route::get('ksa/tanam-bulanan',            [UserKsaTanamBulananController::class, 'index'])->name('ksa.tanam.bulanan.index');

    Route::get('ksa/tanam-total/export',       [UserKsaTanamTotalController::class, 'export'])->name('ksa.tanam.total.export');
    Route::get('ksa/tanam-total',              [UserKsaTanamTotalController::class, 'index'])->name('ksa.tanam.total.index');

    Route::get('ksa/panen/export',             [UserKsaPanenController::class, 'export'])->name('ksa.panen.export');
    Route::get('ksa/panen',                    [UserKsaPanenController::class, 'index'])->name('ksa.panen.index');

    Route::get('ksa/panen-bulanan/export',     [UserKsaPanenBulananController::class, 'export'])->name('ksa.panen.bulanan.export');
    Route::get('ksa/panen-bulanan',            [UserKsaPanenBulananController::class, 'index'])->name('ksa.panen.bulanan.index');

    Route::get('ksa/panen-total/export',       [UserKsaPanenTotalController::class, 'export'])->name('ksa.panen.total.export');
    Route::get('ksa/panen-total',              [UserKsaPanenTotalController::class, 'index'])->name('ksa.panen.total.index');

    Route::get('ksa/produksi/export',          [UserKsaProduksiController::class, 'export'])->name('ksa.produksi.export');
    Route::get('ksa/produksi',                 [UserKsaProduksiController::class, 'index'])->name('ksa.produksi.index');

    Route::get('ksa/produksi-bulanan/export',  [UserKsaProduksiBulananController::class, 'export'])->name('ksa.produksi.bulanan.export');
    Route::get('ksa/produksi-bulanan',         [UserKsaProduksiBulananController::class, 'index'])->name('ksa.produksi.bulanan.index');

    Route::get('ksa/produksi-total/export',    [UserKsaProduksiTotalController::class, 'export'])->name('ksa.produksi.total.export');
    Route::get('ksa/produksi-total',           [UserKsaProduksiTotalController::class, 'index'])->name('ksa.produksi.total.index');

    // ── IP Padi ──────────────────────────────────────────────────────────────
    Route::get('ip/padi/export', [UserIpPadiController::class, 'export'])->name('ip.padi.export');
    Route::get('ip/padi',        [UserIpPadiController::class, 'index'])->name('ip.padi.index');
    Route::get('ip/padi/{id}',   [UserIpPadiController::class, 'show'])->name('ip.padi.show');

    // ── LBS ──────────────────────────────────────────────────────────────────
    Route::get('lbs/export', [UserLbsController::class, 'export'])->name('lbs.export');
    Route::get('lbs',        [UserLbsController::class, 'index'])->name('lbs.index');
    Route::get('lbs/{id}',   [UserLbsController::class, 'show'])->name('lbs.show');

    // ── Grafik (semua pakai User\Grafik*Controller) ───────────────────────────
    Route::prefix('grafik')->name('grafik.')->group(function () {

        // LTT & LTP
        Route::get('ltt/tanam',         [UserGrafikLttTanamController::class, 'index'])->name('ltt.tanam');
        Route::get('ltt/tanam/pdf',     [UserGrafikLttTanamController::class, 'exportPdf'])->name('ltt.tanam.pdf');
        
        Route::get('ltt/panen',         [UserGrafikLttPanenController::class, 'index'])->name('ltt.panen');
        Route::get('ltt/panen/pdf',     [UserGrafikLttPanenController::class, 'exportPdf'])->name('ltt.panen.pdf');
        
        // KSA
        Route::get('ksa/tanam',         [UserGrafikKsaTanamController::class, 'index'])->name('ksa.tanam');
        Route::get('ksa/tanam/pdf',     [UserGrafikKsaTanamController::class, 'exportPdf'])->name('ksa.tanam.pdf');
        
        Route::get('ksa/panen',         [UserGrafikKsaPanenController::class, 'index'])->name('ksa.panen');
        Route::get('ksa/panen/pdf',     [UserGrafikKsaPanenController::class, 'exportPdf'])->name('ksa.panen.pdf');
        
        Route::get('ksa/produksi',      [UserGrafikKsaProduksiController::class, 'index'])->name('ksa.produksi');
        Route::get('ksa/produksi/pdf',  [UserGrafikKsaProduksiController::class, 'exportPdf'])->name('ksa.produksi.pdf');
        
        // IP Padi
        Route::get('ip/padi',           [UserGrafikIpPadiController::class, 'index'])->name('ip.padi');
        Route::get('ip/padi/pdf',       [UserGrafikIpPadiController::class, 'exportPdf'])->name('ip.padi.pdf');
        
        // LBS
        Route::get('lbs',               [UserGrafikLbsController::class, 'index'])->name('lbs');
        Route::get('lbs/pdf',           [UserGrafikLbsController::class, 'exportPdf'])->name('lbs.pdf');
        
        // Alias lama (backward compat)
        Route::get('sanding/lbs',       [UserGrafikLbsController::class, 'index'])->name('sanding.lbs');
        Route::get('sanding/ip/padi',   [UserGrafikIpPadiController::class, 'index'])->name('sanding.ip.padi');

    }); // end grafik

}); // end user group

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:master_admin'])->prefix('master-admin')->name('admin.')->group(function () {
        Route::resource('admins', AdminController::class);
    });

    Route::middleware(['role:master_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('kabupaten', KabupatenController::class);
        Route::resource('kecamatan', KecamatanController::class);
    });

    // ── REKAP ────────────────────────────────────────────────────────────────
    Route::prefix('rekap')->name('rekap.')->group(function () {

        Route::get('bulanan/tanam/export',       [RekapBulananTanamController::class, 'export'])->name('bulanan.tanam.export');
        Route::get('bulanan/tanam',              [RekapBulananTanamController::class, 'index'])->name('bulanan.tanam.index');
        Route::get('bulanan/tanam/{rekapBulananTanam}', [RekapBulananTanamController::class, 'show'])->name('bulanan.tanam.show');

        Route::get('tahunan/tanam/export',             [RekapTahunanTanamController::class, 'export'])->name('tahunan.tanam.export');
        Route::get('tahunan/tanam',                    [RekapTahunanTanamController::class, 'index'])->name('tahunan.tanam.index');

        Route::get('bulanan/panen/export',       [RekapBulananPanenController::class, 'export'])->name('bulanan.panen.export');
        Route::get('bulanan/panen',              [RekapBulananPanenController::class, 'index'])->name('bulanan.panen.index');
        Route::get('bulanan/panen/{rekapBulananPanen}', [RekapBulananPanenController::class, 'show'])->name('bulanan.panen.show');
       

        Route::get('tahunan/panen/export',             [RekapTahunanPanenController::class, 'export'])->name('tahunan.panen.export');
        Route::get('tahunan/panen',                    [RekapTahunanPanenController::class, 'index'])->name('tahunan.panen.index');

        Route::get('harian/tanam/export', [RekapHarianTanamController::class, 'export'])->name('harian.tanam.export');
        Route::get('harian/tanam',           [RekapHarianTanamController::class, 'index'])->name('harian.tanam.index');
        Route::get('harian/tanam/create',    [RekapHarianTanamController::class, 'create'])->name('harian.tanam.create');
        Route::post('harian/tanam',          [RekapHarianTanamController::class, 'store'])->name('harian.tanam.store');
        Route::get('harian/tanam/{id}/edit', [RekapHarianTanamController::class, 'edit'])->name('harian.tanam.edit');
        Route::put('harian/tanam/{id}',      [RekapHarianTanamController::class, 'update'])->name('harian.tanam.update');
        Route::delete('harian/tanam/{id}',   [RekapHarianTanamController::class, 'destroy'])->name('harian.tanam.destroy');

        Route::get('harian/panen/export', [RekapHarianPanenController::class, 'export'])->name('harian.panen.export');
        Route::get('harian/panen',           [RekapHarianPanenController::class, 'index'])->name('harian.panen.index');
        Route::get('harian/panen/create',    [RekapHarianPanenController::class, 'create'])->name('harian.panen.create');
        Route::post('harian/panen',          [RekapHarianPanenController::class, 'store'])->name('harian.panen.store');
        Route::get('harian/panen/{id}/edit', [RekapHarianPanenController::class, 'edit'])->name('harian.panen.edit');
        Route::put('harian/panen/{id}',      [RekapHarianPanenController::class, 'update'])->name('harian.panen.update');
        Route::delete('harian/panen/{id}',   [RekapHarianPanenController::class, 'destroy'])->name('harian.panen.destroy');

    }); // end rekap

    // ── KSA ──────────────────────────────────────────────────────────────────
    Route::prefix('ksa')->name('ksa.')->group(function () {

        Route::get('tanam/export', [KsaTanamController::class, 'export'])->name('tanam.export');
        Route::get('tanam',        [KsaTanamController::class, 'index'])->name('tanam.index');

        Route::get('tanam-bulanan/export',                      [KsaTanamBulananController::class, 'export'])->name('tanam.bulanan.export');
        Route::get('tanam-bulanan',                             [KsaTanamBulananController::class, 'index'])->name('tanam.bulanan.index');
        Route::get('tanam-bulanan/create',                      [KsaTanamBulananController::class, 'create'])->name('tanam.bulanan.create');
        Route::post('tanam-bulanan',                            [KsaTanamBulananController::class, 'store'])->name('tanam.bulanan.store');
        Route::get('tanam-bulanan/{kabupaten_id}/edit',         [KsaTanamBulananController::class, 'edit'])->name('tanam.bulanan.edit');
        Route::put('tanam-bulanan/{kabupaten_id}',    [KsaTanamBulananController::class, 'update'])->name('tanam.bulanan.update');
        Route::delete('tanam-bulanan/{kabupaten_id}', [KsaTanamBulananController::class, 'destroy'])->name('tanam.bulanan.destroy');

        Route::get('tanam-total/export', [KsaTanamTotalController::class, 'export'])->name('tanam.total.export');
        Route::get('tanam-total',        [KsaTanamTotalController::class, 'index'])->name('tanam.total.index');

        Route::get('panen/export', [KsaPanenController::class, 'export'])->name('panen.export');
        Route::get('panen',        [KsaPanenController::class, 'index'])->name('panen.index');

        Route::get('panen-bulanan/export',                      [KsaPanenBulananController::class, 'export'])->name('panen.bulanan.export');
        Route::get('panen-bulanan',                             [KsaPanenBulananController::class, 'index'])->name('panen.bulanan.index');
        Route::get('panen-bulanan/create',                      [KsaPanenBulananController::class, 'create'])->name('panen.bulanan.create');
        Route::post('panen-bulanan',                            [KsaPanenBulananController::class, 'store'])->name('panen.bulanan.store');
        Route::get('panen-bulanan/{kabupaten_id}/edit',         [KsaPanenBulananController::class, 'edit'])->name('panen.bulanan.edit');
        Route::put('panen-bulanan/{kabupaten_id}',    [KsaPanenBulananController::class, 'update'])->name('panen.bulanan.update');
        Route::delete('panen-bulanan/{kabupaten_id}', [KsaPanenBulananController::class, 'destroy'])->name('panen.bulanan.destroy');

        Route::get('panen-total/export', [KsaPanenTotalController::class, 'export'])->name('panen.total.export');
        Route::get('panen-total',        [KsaPanenTotalController::class, 'index'])->name('panen.total.index');

        Route::get('produksi/export', [KsaProduksiController::class, 'export'])->name('produksi.export');
        Route::get('produksi',        [KsaProduksiController::class, 'index'])->name('produksi.index');

        Route::get('produksi-bulanan/export',                   [KsaProduksiBulananController::class, 'export'])->name('produksi.bulanan.export');
        Route::get('produksi-bulanan',                          [KsaProduksiBulananController::class, 'index'])->name('produksi.bulanan.index');
        Route::get('produksi-bulanan/create',                   [KsaProduksiBulananController::class, 'create'])->name('produksi.bulanan.create');
        Route::post('produksi-bulanan',                         [KsaProduksiBulananController::class, 'store'])->name('produksi.bulanan.store');
        Route::get('produksi-bulanan/{kabupaten_id}/edit',      [KsaProduksiBulananController::class, 'edit'])->name('produksi.bulanan.edit');
        Route::put('produksi-bulanan/{kabupaten_id}',    [KsaProduksiBulananController::class, 'update'])->name('produksi.bulanan.update');
        Route::delete('produksi-bulanan/{kabupaten_id}', [KsaProduksiBulananController::class, 'destroy'])->name('produksi.bulanan.destroy');


        Route::get('produksi-total/export', [KsaProduksiTotalController::class, 'export'])->name('produksi.total.export');
        Route::get('produksi-total',        [KsaProduksiTotalController::class, 'index'])->name('produksi.total.index');

    }); // end ksa

    // ── IP PADI ──────────────────────────────────────────────────────────────
    Route::prefix('ip')->name('ip.')->group(function () {
        Route::get('padi/export', [IpPadiController::class, 'export'])->name('padi.export');
        Route::get('padi',        [IpPadiController::class, 'index'])->name('padi.index');
        Route::get('padi/{id}',   [IpPadiController::class, 'show'])->name('padi.show');
    });

    // ── LBS ──────────────────────────────────────────────────────────────────
    Route::prefix('lbs')->name('lbs.')->group(function () {
        Route::get('/export', [LbsController::class, 'export'])->name('export');
        Route::get('/',       [LbsController::class, 'index'])->name('index');
        Route::get('/create',  [LbsController::class, 'create'])->name('create');
        Route::post('/',       [LbsController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LbsController::class, 'edit'])->name('edit');
        Route::put('/{id}',    [LbsController::class, 'update'])->name('update');
        Route::delete('/{id}', [LbsController::class, 'destroy'])->name('destroy');
        Route::get('/{id}',   [LbsController::class, 'show'])->name('show');
    });

    // ── GRAFIK (admin) ───────────────────────────────────────────────────────
    Route::prefix('grafik')->name('grafik.')->group(function () {

        Route::get('ltt/tanam',     [GrafikLttTanamController::class, 'index'])->name('ltt.tanam');
        Route::get('ltt/panen',     [GrafikLttPanenController::class, 'index'])->name('ltt.panen');
        Route::get('ltt/tanam/pdf', [GrafikLttTanamController::class, 'exportPdf'])->name('ltt.tanam.pdf');
        Route::get('ltt/panen/pdf', [GrafikLttPanenController::class, 'exportPdf'])->name('ltt.panen.pdf');

        Route::get('ksa/tanam',        [GrafikKsaTanamController::class, 'index'])->name('ksa.tanam');
        Route::get('ksa/panen',        [GrafikKsaPanenController::class, 'index'])->name('ksa.panen');
        Route::get('ksa/produksi',     [GrafikKsaProduksiController::class, 'index'])->name('ksa.produksi');
        Route::get('ksa/tanam/pdf',    [GrafikKsaTanamController::class,    'exportPdf'])->name('ksa.tanam.pdf');
        Route::get('ksa/panen/pdf',    [GrafikKsaPanenController::class,    'exportPdf'])->name('ksa.panen.pdf');
        Route::get('ksa/produksi/pdf', [GrafikKsaProduksiController::class, 'exportPdf'])->name('ksa.produksi.pdf');

        Route::get('ip/padi',             [GrafikIpPadiController::class, 'index'])->name('ip.padi');
        Route::get('ip/padi/export/pdf',  [GrafikIpPadiController::class, 'exportPdf'])->name('ip.pdf');
        
        Route::get('sanding/lbs',             [GrafikLbsController::class, 'index'])->name('sanding.lbs');
        Route::get('sanding/lbs/export/pdf',  [GrafikLbsController::class, 'exportPdf'])->name('sanding.lbs.pdf');
        
    }); // end grafik

}); // end auth

require __DIR__.'/auth.php';