<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak perlu migration baru karena menggunakan data dari tabel yang sudah ada:
        // - ksa_luas_tanam
        // - ksa_luas_panen
        // - ksa_produksi
        // - luas_baku_sawah
        
        // Namun jika ingin menyimpan cache/snapshot grafik:
        Schema::create('grafik', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_grafik'); // sanding_tanam, sanding_panen, sanding_produksi, ip_padi, sanding_lbs
            $table->integer('tahun_awal');
            $table->integer('tahun_akhir');
            $table->string('bulan')->nullable();
            $table->json('data_grafik'); // Menyimpan data grafik dalam JSON
            $table->timestamps();
            
            $table->index(['tipe_grafik', 'tahun_awal', 'tahun_akhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grafik');
    }
};