<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    protected $fillable = ['kabupaten_id', 'nama_kecamatan'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
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