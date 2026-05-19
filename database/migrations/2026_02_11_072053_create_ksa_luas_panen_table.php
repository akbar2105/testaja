<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ksa_luas_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->string('bulan', 20);
            $table->integer('tahun');
            $table->decimal('luas_panen', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['kabupaten_id', 'bulan', 'tahun']);
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ksa_luas_panen');
    }
};
