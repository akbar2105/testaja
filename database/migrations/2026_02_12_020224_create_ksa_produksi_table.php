<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ksa_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->string('bulan', 20);
            $table->integer('tahun');
            $table->decimal('produksi', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['kabupaten_id', 'bulan', 'tahun']);
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ksa_produksi');
    }
};