<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapTahunanPanen extends Model
{
    protected $table = 'rekap_tahunan_panen';

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
     * Label periode tahun: "Okt {tahun-1} - Sept {tahun}"
     */
    public function getPeriodeLabelAttribute(): string
    {
        return "Okt " . ($this->tahun - 1) . " - Sept " . $this->tahun;
    }

    /**
     * Hitung ulang total dari rekap bulanan (Okt s.d. Sept).
     */
    public static function recalculate(int $kabupatenId, int $tahun): self
    {
        $total = RekapBulananPanen::where('kabupaten_id', $kabupatenId)
            ->where(function ($q) use ($tahun) {
                $q->where(function ($q2) use ($tahun) {
                    $q2->whereYear('bulan', $tahun - 1)
                       ->whereMonth('bulan', '>=', 10);
                })->orWhere(function ($q2) use ($tahun) {
                    $q2->whereYear('bulan', $tahun)
                       ->whereMonth('bulan', '<=', 9);
                });
            })
            ->sum('luas_panen');

        return self::updateOrCreate(
            ['kabupaten_id' => $kabupatenId, 'tahun' => $tahun],
            ['total' => $total]
        );
    }
}