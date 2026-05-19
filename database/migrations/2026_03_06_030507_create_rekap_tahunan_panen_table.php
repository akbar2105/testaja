<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_tahunan_panen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kabupaten_id');
            $table->integer('tahun');           // Tahun periode (misal: 2024 = Okt 2023 - Sept 2024)
            $table->decimal('total', 15, 3)->default(0); // Total LTP Okt-Sept (Ha)
            $table->timestamps();

            $table->foreign('kabupaten_id')->references('id')->on('kabupaten')->onDelete('cascade');
            $table->unique(['kabupaten_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_tahunan_panen');
    }
};