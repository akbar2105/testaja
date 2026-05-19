<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indeks_pertanaman_padi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->integer('tahun'); // Tahun IP (2018, 2019, 2020, dst)
            $table->decimal('total_luas_tanam', 12, 2)->default(0); // Total luas tanam dalam 1 tahun
            $table->decimal('luas_baku_sawah', 12, 2)->default(0); // LBS yang digunakan untuk tahun ini
            $table->decimal('ip', 10, 2)->default(0); // Indeks Pertanaman (total_luas_tanam / luas_baku_sawah)
            $table->integer('lbs_tahun_referensi')->nullable(); // Tahun LBS yang digunakan (2018, 2024, dst)
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['kabupaten_id', 'tahun']);
            $table->index('tahun');
            $table->index('lbs_tahun_referensi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indeks_pertanaman_padi');
    }
};