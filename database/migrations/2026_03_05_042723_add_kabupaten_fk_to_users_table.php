<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Nama file ini harus timestamp LEBIH BARU dari migration create_kabupaten_table
// agar FK dibuat setelah tabel kabupaten sudah ada.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('kabupaten_id')
                  ->references('id')
                  ->on('kabupaten')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kabupaten_id']);
        });
    }
};