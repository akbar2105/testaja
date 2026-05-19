<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KsaLuasTanam extends Model
{
    protected $table = 'ksa_luas_tanam';

    protected $fillable = [
        'kabupaten_id',
        'bulan',       // integer 1-12
        'tahun',       // tahun kalender biasa
        'luas_tanam',
        'keterangan',
    ];

    protected $casts = [
        'bulan'      => 'integer',
        'tahun'      => 'integer',
        'luas_tanam' => 'decimal:2',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // ── Model Events → Auto-sync IP ───────────────────────────────────────────
    //
    // Cara penyimpanan di DB (dari KsaTanamBulananController):
    //   User input "tahun 2024":
    //     bulan 10, 11, 12 → DB: tahun=2024, bulan=10/11/12  → periode IP = 2024
    //     bulan 1 – 9      → DB: tahun=2024, bulan=1-9       → periode IP = 2023
    //
    //   Maka konversi bulan kalender → tahun periode Oktober:
    //     bulan >= 10  → periodeTahun = tahun (data ini adalah awal periode)
    //     bulan <= 9   → periodeTahun = tahun - 1 (data ini adalah akhir periode sebelumnya)
    //
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($ksa) {
            self::triggerIpSync($ksa->kabupaten_id, $ksa->bulan, $ksa->tahun);
        });

        static::updated(function ($ksa) {
            // Sync nilai baru
            self::triggerIpSync($ksa->kabupaten_id, $ksa->bulan, $ksa->tahun);

            // Jika bulan atau tahun berubah, sync nilai lama juga
            // agar IP periode lama ikut terupdate (misal nilai dihapus/dipindah)
            $bulanBerubah = $ksa->wasChanged('bulan');
            $tahunBerubah = $ksa->wasChanged('tahun');

            if ($bulanBerubah || $tahunBerubah) {
                $oldBulan = (int) $ksa->getOriginal('bulan');
                $oldTahun = (int) $ksa->getOriginal('tahun');
                self::triggerIpSync($ksa->kabupaten_id, $oldBulan, $oldTahun);
            }
        });

        static::deleted(function ($ksa) {
            self::triggerIpSync($ksa->kabupaten_id, $ksa->bulan, $ksa->tahun);
        });
    }

    /**
     * Konversi bulan + tahun kalender ke tahun periode Oktober,
     * lalu panggil IndeksPertanamanPadi::syncFromKsaAndLbs().
     *
     * Logika:
     *   - bulan 10, 11, 12 → data ini adalah Okt/Nov/Des awal periode T
     *                         → periodeTahun = tahun (T)
     *   - bulan 1 – 9      → data ini adalah Jan–Sep akhir periode T-1
     *                         → periodeTahun = tahun - 1 (T-1)
     *
     * Contoh:
     *   DB: tahun=2024, bulan=10 → triggerIpSync → periodeTahun=2024
     *   DB: tahun=2025, bulan=1  → triggerIpSync → periodeTahun=2024  ← sama periodenya!
     */
    protected static function triggerIpSync(int $kabupatenId, int $bulan, int $tahun): void
    {
        try {
            $periodeTahun = ($bulan >= 10) ? $tahun : ($tahun - 1);

            \Log::info('KsaLuasTanam: trigger IP sync', [
                'kabupaten_id'  => $kabupatenId,
                'bulan'         => $bulan,
                'tahun_kalender'=> $tahun,
                'periode_tahun' => $periodeTahun,
            ]);

            IndeksPertanamanPadi::syncFromKsaAndLbs($periodeTahun, $kabupatenId);

        } catch (\Throwable $e) {
            \Log::error('KsaLuasTanam: IP sync gagal', [
                'kabupaten_id' => $kabupatenId,
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Urutan bulan Oktober–September (sesuai tampilan sanding KSA).
     * Key = integer bulan (1–12), Value = singkatan nama.
     */
    public static function getBulanList(): array
    {
        return [
            10 => 'Okt',  11 => 'Nov',   12 => 'Des',
             1 => 'Jan',   2 => 'Feb',    3 => 'Maret',
             4 => 'April', 5 => 'Mei',    6 => 'Juni',
             7 => 'Juli',  8 => 'Agust',  9 => 'Sept',
        ];
    }

    /** Nama bulan lengkap */
    public static function getBulanListLengkap(): array
    {
        return [
            1 => 'Januari',  2 => 'Februari', 3 => 'Maret',
            4 => 'April',    5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',     8 => 'Agustus',   9 => 'September',
           10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public static function getNamaBulan(int $bulan): string
    {
        return self::getBulanListLengkap()[$bulan] ?? '-';
    }

    /**
     * Tahun yang tersedia (dari data di DB).
     * Karena periode Okt–Sep, "tahun sanding" = tahun bulan Oktober (T),
     * sedangkan Jan–Sep masih dalam sanding tahun T (misal 2024-okt s/d 2025-sep = sanding 2024).
     * Namun di DB kita simpan tahun kalender biasa.
     */
    public static function getAvailableYears(): array
    {
        $years = self::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($years)) {
            $y = (int) date('Y');
            return range($y, $y - 5);
        }
        return $years;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByBulan($q, int $bulan) { return $q->where('bulan', $bulan); }
    public function scopeByTahun($q, int $tahun) { return $q->where('tahun', $tahun); }

    // ── Accessor ──────────────────────────────────────────────────────────────

    public function getNamaBulanAttribute(): string
    {
        return self::getNamaBulan($this->bulan);
    }
}