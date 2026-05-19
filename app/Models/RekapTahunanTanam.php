<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapTahunanTanam extends Model
{
    protected $table = 'rekap_tahunan_tanam';

    protected $fillable = [
        'kabupaten_id',
        'tahun',
        'total',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'total' => 'decimal:3',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    /**
     * Label periode tahun: "Jan {tahun} - Des {tahun}"
     */
    public function getPeriodeLabelAttribute(): string
    {
        return "Jan " . $this->tahun . " - Des " . $this->tahun;
    }

    /**
     * Hitung ulang total dari rekap bulanan (Januari s.d. Desember).
     * RekapBulananTanam menyimpan kolom: tahun (int), januari, februari, ..., desember, total
     */
    public static function recalculate(int $kabupatenId, int $tahun): self
    {
        $rekap = RekapBulananTanam::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $tahun)
            ->first();

        // Jika ada rekap bulanan, ambil total-nya
        // total di rekap bulanan sudah = Σ jan-des
        $total = $rekap ? (float) $rekap->total : 0;

        return self::updateOrCreate(
            ['kabupaten_id' => $kabupatenId, 'tahun' => $tahun],
            ['total' => $total]
        );
    }

    /**
     * Hitung ulang dari semua kolom bulanan secara manual (tanpa model bulanan).
     * Berguna jika ingin agregat langsung dari DB.
     */
    public static function recalculateFromDB(int $kabupatenId, int $tahun): self
    {
        $total = RekapBulananTanam::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $tahun)
            ->selectRaw('
                COALESCE(SUM(januari), 0)   + COALESCE(SUM(februari), 0)  +
                COALESCE(SUM(maret), 0)     + COALESCE(SUM(april), 0)     +
                COALESCE(SUM(mei), 0)       + COALESCE(SUM(juni), 0)      +
                COALESCE(SUM(juli), 0)      + COALESCE(SUM(agustus), 0)   +
                COALESCE(SUM(september), 0) + COALESCE(SUM(oktober), 0)   +
                COALESCE(SUM(november), 0)  + COALESCE(SUM(desember), 0)
                AS grand_total
            ')
            ->value('grand_total') ?? 0;

        return self::updateOrCreate(
            ['kabupaten_id' => $kabupatenId, 'tahun' => $tahun],
            ['total' => $total]
        );
    }
}