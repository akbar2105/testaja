<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';
    protected $fillable = ['nama_kabupaten'];

    // ── Tambahkan relasi ke RekapBulananProduksi ──
    public function rekapBulananProduksi()
    {
        return $this->hasMany(RekapBulananProduksi::class, 'kabupaten_id', 'id');
    }

    // Relasi yang sudah ada (tidak diubah)
    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }

    public function rekapBulananTanam()
    {
        return $this->hasMany(RekapBulananTanam::class);
    }

    public function rekapBulananPanen()
    {
        return $this->hasMany(RekapBulananPanen::class);
    }

    public function rekapHarianTanam()
    {
        return $this->hasMany(RekapHarianTanam::class);
    }

    public function rekapHarianPanen()
    {
        return $this->hasMany(RekapHarianPanen::class);
    }
}