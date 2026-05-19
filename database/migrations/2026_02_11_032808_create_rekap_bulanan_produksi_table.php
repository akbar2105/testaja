<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekap_bulanan_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->year('tahun');
            
            // Produksi per bulan (dalam Ton-GKG)
            $table->decimal('januari', 12, 2)->default(0);
            $table->decimal('februari', 12, 2)->default(0);
            $table->decimal('maret', 12, 2)->default(0);
            $table->decimal('april', 12, 2)->default(0);
            $table->decimal('mei', 12, 2)->default(0);
            $table->decimal('juni', 12, 2)->default(0);
            $table->decimal('juli', 12, 2)->default(0);
            $table->decimal('agustus', 12, 2)->default(0);
            $table->decimal('september', 12, 2)->default(0);
            $table->decimal('oktober', 12, 2)->default(0);
            $table->decimal('november', 12, 2)->default(0);
            $table->decimal('desember', 12, 2)->default(0);
            
            // Total produksi (auto calculated)
            $table->decimal('total', 12, 2)->default(0);
            
            $table->timestamps();
            
            // Unique constraint: satu kabupaten hanya punya satu data per tahun
            $table->unique(['kabupaten_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_bulanan_produksi');
    }
};