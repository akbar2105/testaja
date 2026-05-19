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
        Schema::create('rekap_bulanan_tanam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained('kabupaten')->onDelete('cascade');
            $table->integer('tahun');
            
            // Data per bulan (Januari - Desember)
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
            
            // Total keseluruhan
            $table->decimal('total', 12, 2)->default(0);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Unique constraint: satu kabupaten satu tahun satu record
            $table->unique(['kabupaten_id', 'tahun']);
            
            // Index untuk query cepat
            $table->index(['kabupaten_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_bulanan_tanam');
    }
};