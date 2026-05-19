<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luas_baku_sawah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->integer('tahun'); // 2018, 2024, 2029, dst (kelipatan 5 tahun + 4)
            $table->decimal('luas_baku_sawah', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['kabupaten_id', 'tahun']);
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luas_baku_sawah');
    }
};