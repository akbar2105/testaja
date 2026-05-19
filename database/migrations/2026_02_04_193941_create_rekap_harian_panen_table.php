<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_harian_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->onDelete('cascade');
            $table->date('tanggal');
            
            // Field untuk 31 hari
            for ($i = 1; $i <= 31; $i++) {
                $table->decimal('tgl_' . $i, 10, 2)->default(0);
            }
            
            // Field tambahan
            $table->decimal('total_panen', 10, 2)->default(0)->comment('LTT Reguler - Total harian tgl 1-31');
            $table->decimal('oplah', 10, 2)->default(0)->nullable()->comment('Padi OPLAH');
            $table->decimal('gogo', 10, 2)->default(0)->nullable()->comment('Padi GOGO');
            $table->decimal('csr', 10, 2)->default(0)->nullable()->comment('Padi CSR');
            $table->decimal('total_ltp', 10, 2)->default(0)->nullable()->comment('Total LTT = total_panen + oplah + gogo + csr');
            $table->decimal('realisasi', 10, 2)->default(0)->nullable()->comment('Target - Total LTT');
            $table->decimal('target', 10, 2)->default(0)->nullable();
            
            $table->text('keterangan')->nullable();
            
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['kecamatan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_harian_panen');
    }
};