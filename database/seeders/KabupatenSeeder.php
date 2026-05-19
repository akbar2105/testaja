<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kabupaten;

class KabupatenSeeder extends Seeder
{
    public function run(): void
    {
        $kabupaten = [
            'OGAN KOMERING ULU',
            'OGAN KOMERING ILIR',
            'MUARA ENIM',
            'LAHAT',
            'MUSI RAWAS',
            'MUSI BANYUASIN',
            'BANYUASIN',
            'OKU SELATAN',
            'OKU TIMUR',
            'OGAN ILIR',
            'EMPAT LAWANG',
            'PALI',
            'MUSI RAWAS UTARA',
            'PALEMBANG',
            'PRABUMULIH',
            'PAGAR ALAM',
            'LUBUK LINGGAU'
        ];

        foreach ($kabupaten as $kab) {
            Kabupaten::create(['nama_kabupaten' => $kab]);
        }
    }
}