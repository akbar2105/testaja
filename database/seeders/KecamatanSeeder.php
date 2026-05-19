<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Total: 253 kecamatan dari 17 kabupaten di Sumatera Selatan
     *
     * @return void
     */
    public function run(): void
    {
        $kecamatanData = [
            // 1. OGAN KOMERING ULU (13 kecamatan)
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'LENGKITI'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'SOSOH BUAY RAYAB'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'PENGANDONAN'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'MUARA JAYA'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'SEMIDANG AJI'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'ULU OGAN'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'PENINJAUAN'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'SINAR PENINJAUAN'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'LUBUK RAJA'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'LUBUK BATANG'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'BATURAJA TIMUR'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'BATURAJA BARAT'],
            ['kabupaten_id' => 1, 'nama_kecamatan' => 'KEDATON PENINJAUAN RAYA'],

            // 2. OGAN KOMERING ILIR/OKI (18 kecamatan)
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Lempuing'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Lempuing Jaya'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Mesuji'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Sungai Menang'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Mesuji Makmur'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Mesuji Raya'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Tulung Selapan'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Cengal'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Pedamaran'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Pedamaran Timur'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Tanjung Lubuk'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Teluk Gelam'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Kota Kayu Agung'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Sirah Pulau Padang'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Jejawi'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Pampangan'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Pangkalan Lapam'],
            ['kabupaten_id' => 2, 'nama_kecamatan' => 'Air Sugihan'],

            // 3. MUARA ENIM (22 kecamatan)
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Semendo Darat Laut'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Semendo Darat Ulu'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Semendo Darat Tengah'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Tanjung Agung'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Panang Enim'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Rambang'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Lubai'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Lubai Ulu'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Lawang Kidul'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Muara Enim'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Ujan Mas'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Gunung Megang'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Benakat'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Belimbing'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Rambang Niru'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Empat Petulai Dangku'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Gelumbang'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Lembak'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Sungai Rotan'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Muara Belida'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Kelekar'],
            ['kabupaten_id' => 3, 'nama_kecamatan' => 'Belida Darat'],

            // 4. LAHAT (24 kecamatan)
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Merapi Timur'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Merapi Barat'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Merapi Selatan'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Lahat'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Lahat Selatan'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Gumay Talang'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Pseksu'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Pulau Pinang'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Gumay Ulu'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Pagar Gunung'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Tanjung Tebat'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Kota Agung'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Mulak Ulu'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Mulak Sebingkai'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Tanjung Sakti Pumu'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Tanjung Sakti Pumi'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Pajar Bulan'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Suka Merindu'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Jarai'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Muara Payang'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Kikim Barat'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Kikim Tengah'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Kikim Selatan'],
            ['kabupaten_id' => 4, 'nama_kecamatan' => 'Kikim Timur'],

            // 5. MUSI RAWAS (14 kecamatan)
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'STL Ulu Terawas'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Selangit'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Sumberharta'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Tugumulyo'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Purwodadi'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Muara Beliti'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'TP Kepungut'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Jayaloka'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Suka Karya'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Muara Kelingi'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'BTS Ulu'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Tuah Negeri'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Muara Lakitan'],
            ['kabupaten_id' => 5, 'nama_kecamatan' => 'Megang Sakti'],

            // 6. MUSI BANYUASIN (15 kecamatan)
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Sanga Desa'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Babat Toman'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Batanghari Leko'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Plakat Tinggi'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Lawang Wetan'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Sungai Keruh'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Jirak Jaya'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Sekayu'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Lais'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Sungai Lilin'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Keluang'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Babat Supat'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Bayung Lencir'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Lalan'],
            ['kabupaten_id' => 6, 'nama_kecamatan' => 'Tungkal Jaya'],

            // 7. BANYUASIN (21 kecamatan)
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Rantau Bayur'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Betung'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Suak Tapeh'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Pulau Rimau'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Tungkal Ilir'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Selat Penuguan'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Banyuasin III'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Sembawa'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Talang Kelapa'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Tanjung Lago'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Banyuasin I'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Air Kumbang'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Rambutan'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Muara Padang'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Muara Sugihan'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Makarti Jaya'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Air Salek'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Banyuasin II'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Karang Agung Ilir'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Muara Telang'],
            ['kabupaten_id' => 7, 'nama_kecamatan' => 'Sumber Marga Telang'],

            // 8. OKU SELATAN (19 kecamatan)
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Mekakau Ilir'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Banding Agung'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Warkuk Ranau Selatan'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buay Pematang Ribu Ranau Tengah'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buay Pemaca'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Simpang'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buana Pemaca'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Muaradua'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buay Rawan'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buay Sandang Aji'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Tiga Dihaji'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Buay Runjung'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Runjung Agung'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Kisam Tinggi'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Muaradua Kisam'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Kisam Ilir'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Pulau Beringin'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Sindang Danau'],
            ['kabupaten_id' => 8, 'nama_kecamatan' => 'Sungai Are'],

            // 9. OKU TIMUR (20 kecamatan)
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Martapura'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Bunga Mayang'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Jayapura'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'BP Peliung'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Buay Madang'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Buay Madang Timur'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'BP Bangsa Raja'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Madang Suku I'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Madang Suku II'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Madang Suku III'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang Madang Raya'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang Jaya'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang Mulya'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang II'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Belitang III'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Semendawai Suku III'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Semendawai Timur'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Semendawai Barat'],
            ['kabupaten_id' => 9, 'nama_kecamatan' => 'Cempaka'],

            // 10. OGAN ILIR (16 kecamatan)
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Muara Kuang'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Rambang Kuang'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Lubuk Keliat'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Tanjung Batu'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Payaraman'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Rantau Alai'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Kandis'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Tanjung Raja'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Rantau Panjang'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Sungai Pinang'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Pemulutan'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Pemulutan Selatan'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Pemulutan Barat'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Indralaya'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Indralaya Utara'],
            ['kabupaten_id' => 10, 'nama_kecamatan' => 'Indralaya Selatan'],

            // 11. EMPAT LAWANG (10 kecamatan)
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Muara Pinang'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Lintang Kanan'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Pendopo Induk'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Pendopo Barat'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Pasemah Air Keruh'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Ulu Musi'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Sikap Dalam'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Talang Padang'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Tebing Tinggi'],
            ['kabupaten_id' => 11, 'nama_kecamatan' => 'Saling'],

            // 12. PALI (5 kecamatan)
            ['kabupaten_id' => 12, 'nama_kecamatan' => 'Talang Ubi'],
            ['kabupaten_id' => 12, 'nama_kecamatan' => 'Tanah Abang'],
            ['kabupaten_id' => 12, 'nama_kecamatan' => 'Abab'],
            ['kabupaten_id' => 12, 'nama_kecamatan' => 'Penukal'],
            ['kabupaten_id' => 12, 'nama_kecamatan' => 'Penukal Utara'],

            // 13. MURATARA (7 kecamatan)
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Ulu Rawas'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Karang Jaya'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Rawas Ulu'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Rupit'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Karang Dapo'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Rawas Ilir'],
            ['kabupaten_id' => 13, 'nama_kecamatan' => 'Nibung'],

            // 14. PALEMBANG (18 kecamatan)
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Alang-alang Lebar'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Bukit Kecil'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Gandus'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Ilir Barat I'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Ilir Barat II'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Ilir Timur I'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Ilir Timur II'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Ilir Timur III'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Jakabaring'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Kalidoni'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Kemuning'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Kertapati'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Plaju'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Sako'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Seberang Ulu I'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Seberang Ulu II'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Sematang Borang'],
            ['kabupaten_id' => 14, 'nama_kecamatan' => 'Sukarami'],

            // 15. PRABUMULIH (6 kecamatan)
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Rambang Kapak Tengah'],
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Prabumulih Timur'],
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Prabumulih Selatan'],
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Prabumulih Barat'],
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Prabumulih Utara'],
            ['kabupaten_id' => 15, 'nama_kecamatan' => 'Cambai'],

            // 16. PAGAR ALAM (5 kecamatan)
            ['kabupaten_id' => 16, 'nama_kecamatan' => 'Dempo Selatan'],
            ['kabupaten_id' => 16, 'nama_kecamatan' => 'Dempo Tengah'],
            ['kabupaten_id' => 16, 'nama_kecamatan' => 'Dempo Utara'],
            ['kabupaten_id' => 16, 'nama_kecamatan' => 'Pagar Alam Selatan'],
            ['kabupaten_id' => 16, 'nama_kecamatan' => 'Pagar Alam Utara'],

            // 17. LUBUKLINGGAU (8 kecamatan)
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Barat I'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Barat II'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Selatan I'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Selatan II'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Timur I'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Timur II'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Utara I'],
            ['kabupaten_id' => 17, 'nama_kecamatan' => 'Lubuk Linggau Utara II'],
        ];

        // Insert data dengan batch
        DB::table('kecamatan')->insert($kecamatanData);
        
        echo "\n✅ KecamatanSeeder berhasil!\n";
        echo "   Total: " . count($kecamatanData) . " kecamatan dari 17 kabupaten\n\n";
    }
}