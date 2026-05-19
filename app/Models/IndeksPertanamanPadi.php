<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndeksPertanamanPadi extends Model
{
    use HasFactory;

    protected $table = 'indeks_pertanaman_padi';

    protected $fillable = [
        'kabupaten_id',
        'tahun',               // tahun periode Oktober (T), bukan tahun kalender
        'total_luas_tanam',
        'luas_baku_sawah',
        'ip',
        'lbs_tahun_referensi',
        'keterangan',
    ];

    protected $casts = [
        'total_luas_tanam'    => 'decimal:2',
        'luas_baku_sawah'     => 'decimal:2',
        'ip'                  => 'decimal:2',
        'tahun'               => 'integer',
        'lbs_tahun_referensi' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // ── Auto-Sync Entry Point ─────────────────────────────────────────────────

    /**
     * Dipanggil oleh:
     *   - KsaLuasTanam::boot() saat data KSA berubah
     *   - LuasBakuSawah::boot() saat data LBS berubah
     *
     * @param int      $tahun       Tahun periode Oktober (T)
     *                              misal tahun=2024 → periode Okt 2024 – Sep 2025
     * @param int|null $kabupatenId Jika null, sync semua kabupaten
     */
    public static function syncFromKsaAndLbs(int $tahun, ?int $kabupatenId = null): array
    {
        try {
            \Log::info('IndeksPertanamanPadi: mulai sync', [
                'periode'      => "Okt {$tahun} – Sep " . ($tahun + 1),
                'kabupaten_id' => $kabupatenId ?? 'semua',
            ]);

            $query = Kabupaten::query();
            if ($kabupatenId) {
                $query->where('id', $kabupatenId);
            }
            $kabupatens = $query->get();

            $synced = $deleted = $skipped = 0;

            foreach ($kabupatens as $kab) {
                $result = self::calculateAndSave($kab->id, $tahun);
                match ($result) {
                    'synced'  => $synced++,
                    'deleted' => $deleted++,
                    default   => $skipped++,
                };
            }

            \Log::info('IndeksPertanamanPadi: sync selesai', compact('synced', 'deleted', 'skipped'));

            return ['synced' => $synced, 'deleted' => $deleted, 'skipped' => $skipped];

        } catch (\Throwable $e) {
            \Log::error('IndeksPertanamanPadi: sync gagal', ['error' => $e->getMessage()]);
            return ['synced' => 0, 'deleted' => 0, 'skipped' => 0, 'error' => $e->getMessage()];
        }
    }

    // ── Core Calculation ──────────────────────────────────────────────────────

    /**
     * Hitung dan simpan IP untuk satu kabupaten pada satu tahun periode.
     *
     * ─────────────────────────────────────────────────────────────────────────
     * CARA DATA TERSIMPAN DI TABEL ksa_luas_tanam:
     *
     *   KsaTanamBulananController menyimpan dengan tahun kalender biasa.
     *   User input "tahun=2024":
     *     bulan 10 (Okt) → DB: tahun=2024, bulan=10
     *     bulan 11 (Nov) → DB: tahun=2024, bulan=11
     *     bulan 12 (Des) → DB: tahun=2024, bulan=12
     *
     *   User input "tahun=2025" (masih satu periode dengan 2024!):
     *     bulan  1 (Jan) → DB: tahun=2025, bulan=1
     *     ...
     *     bulan  9 (Sep) → DB: tahun=2025, bulan=9
     *
     *   Jadi untuk menghitung IP periode 2024 (Okt 2024 – Sep 2025):
     *     Ambil: (tahun=2024 AND bulan IN [10,11,12])
     *     PLUS : (tahun=2025 AND bulan IN [1,2,3,4,5,6,7,8,9])
     * ─────────────────────────────────────────────────────────────────────────
     *
     * @param int $kabupatenId
     * @param int $tahun  Tahun periode Oktober (T)
     * @return string  'synced' | 'deleted' | 'skipped'
     */
    protected static function calculateAndSave(int $kabupatenId, int $tahun): string
    {
        // ── 1. Total Luas Tanam KSA periode Okt T – Sep (T+1) ────────────────
        $totalLuasTanam = KsaLuasTanam::where('kabupaten_id', $kabupatenId)
            ->where(function ($q) use ($tahun) {
                // Okt, Nov, Des → tersimpan dengan tahun = T
                $q->where(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun)
                       ->whereIn('bulan', [10, 11, 12]);
                })
                // Jan s.d. Sep → tersimpan dengan tahun = T+1
                ->orWhere(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun + 1)
                       ->whereBetween('bulan', [1, 9]);
                });
            })
            ->sum('luas_tanam');

        \Log::info('KSA sum', [
            'kabupaten_id'     => $kabupatenId,
            'periode'          => "Okt {$tahun} – Sep " . ($tahun + 1),
            'total_luas_tanam' => $totalLuasTanam,
        ]);

        // ── 2. Luas Baku Sawah ────────────────────────────────────────────────
        $lbsTahun      = LuasBakuSawah::getLbsYearForIpYear($tahun);
        $luasBakuSawah = LuasBakuSawah::getLbsForIp($kabupatenId, $tahun);

        \Log::info('LBS', [
            'kabupaten_id'   => $kabupatenId,
            'lbs_tahun'      => $lbsTahun,
            'luas_baku_sawah'=> $luasBakuSawah,
        ]);

        // ── 3. Jika tidak ada data KSA atau LBS = 0, hapus record IP ─────────
        if ((float) $totalLuasTanam === 0.0 || (float) $luasBakuSawah === 0.0) {
            $deleted = self::where('kabupaten_id', $kabupatenId)
                ->where('tahun', $tahun)
                ->delete();

            if ($deleted > 0) {
                \Log::info('IP dihapus (tidak ada data KSA/LBS)', compact('kabupatenId', 'tahun'));
                return 'deleted';
            }
            return 'skipped';
        }

        // ── 4. Hitung IP ──────────────────────────────────────────────────────
        //   IP = Total Luas Tanam (Okt T – Sep T+1) ÷ Luas Baku Sawah
        $ip = round((float) $totalLuasTanam / (float) $luasBakuSawah, 2);

        \Log::info('IP dihitung', [
            'kabupaten_id' => $kabupatenId,
            'tahun'        => $tahun,
            'ip'           => $ip,
            'formula'      => "{$totalLuasTanam} ÷ {$luasBakuSawah}",
        ]);

        // ── 5. Simpan atau update ─────────────────────────────────────────────
        self::updateOrCreate(
            [
                'kabupaten_id' => $kabupatenId,
                'tahun'        => $tahun,
            ],
            [
                'total_luas_tanam'    => $totalLuasTanam,
                'luas_baku_sawah'     => $luasBakuSawah,
                'ip'                  => $ip,
                'lbs_tahun_referensi' => $lbsTahun,
            ]
        );

        return 'synced';
    }

    // ── Recalculate All (dipanggil dari IpPadiController) ────────────────────

    /**
     * Hitung ulang semua IP dari seluruh data KSA + LBS yang ada.
     * Berguna untuk sinkronisasi data lama.
     */
    public static function recalculateAll(): array
    {
        // Kumpulkan semua kombinasi kabupaten_id + tahun periode dari KSA
        $periodeTahuns = KsaLuasTanam::selectRaw('kabupaten_id, tahun, bulan')
            ->get()
            ->map(function ($row) {
                return [
                    'kabupaten_id'  => $row->kabupaten_id,
                    'periode_tahun' => ($row->bulan >= 10) ? $row->tahun : ($row->tahun - 1),
                ];
            })
            ->unique(fn ($item) => $item['kabupaten_id'] . '_' . $item['periode_tahun'])
            ->values();

        $synced = $deleted = $skipped = 0;

        foreach ($periodeTahuns as $item) {
            $result = self::calculateAndSave($item['kabupaten_id'], $item['periode_tahun']);
            match ($result) {
                'synced'  => $synced++,
                'deleted' => $deleted++,
                default   => $skipped++,
            };
        }

        \Log::info('IndeksPertanamanPadi: recalculateAll selesai', compact('synced', 'deleted', 'skipped'));

        return ['synced' => $synced, 'deleted' => $deleted, 'skipped' => $skipped];
    }

    // ── Available Years ───────────────────────────────────────────────────────

    /**
     * Kumpulkan semua tahun PERIODE yang relevan dari 3 sumber:
     *
     *  1. Tabel indeks_pertanaman_padi   → tahun yang sudah dihitung
     *  2. Tabel ksa_luas_tanam           → tahun periode yang bisa dihitung
     *       bulan 10-12 → periodeTahun = tahun DB
     *       bulan 1-9   → periodeTahun = tahun DB - 1
     *  3. Tabel luas_baku_sawah          → pastikan LBS tersedia
     *       (LBS ada = periode tersebut bisa dihitung IP-nya)
     *
     * Dengan cara ini, tahun 2028 akan muncul di dropdown
     * begitu ada data KSA bulan 10/11/12 tahun 2028 ATAU
     * data KSA bulan 1-9 tahun 2029, meski IP belum tersimpan.
     */
    public static function getAvailableYears(): array
    {
        // Sumber 1: tahun yang sudah ada di tabel IP
        $fromIp = self::select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        // Sumber 2: tahun periode dari KSA Tanam
        $fromKsa = KsaLuasTanam::selectRaw('tahun, bulan')
            ->distinct()
            ->get()
            ->map(fn ($row) => (int) $row->bulan >= 10 ? (int) $row->tahun : (int) $row->tahun - 1)
            ->unique()
            ->filter(fn ($y) => $y >= 2000)  // buang nilai tidak masuk akal
            ->toArray();

        // Sumber 3: tahun yang ada LBS-nya (langsung pakai tahun LBS sebagai periode)
        $fromLbs = LuasBakuSawah::select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        // Gabung, unik, urutkan descending
        $all = array_unique(array_merge($fromIp, $fromKsa, $fromLbs));

        if (empty($all)) {
            $current = (int) date('Y');
            return range($current, $current - 5);
        }

        rsort($all);
        return array_values($all);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByKabupaten($query, $kabupatenId)
    {
        return $query->where('kabupaten_id', $kabupatenId);
    }

    public function scopeYearRange($query, $startYear, $endYear)
    {
        return $query->whereBetween('tahun', [$startYear, $endYear]);
    }
}