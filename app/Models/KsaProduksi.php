<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KsaProduksi extends Model
{
    protected $table = 'ksa_produksi';

    protected $fillable = [
        'kabupaten_id',
        'bulan',       // integer 1-12
        'tahun',
        'produksi',    // Ton-GKG
        'keterangan',
    ];

    protected $casts = [
        'bulan'    => 'integer',
        'tahun'    => 'integer',
        'produksi' => 'decimal:2',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Urutan bulan Januari–Desember.
     * Key = integer bulan, Value = nama singkat.
     */
    public static function getBulanList(): array
    {
        return [
             1 => 'Januari',    2 => 'Februari',  3 => 'Maret',
             4 => 'April',      5 => 'Mei',        6 => 'Juni',
             7 => 'Juli',       8 => 'Agustus',    9 => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];
    }

    public static function getBulanListSingkat(): array
    {
        return [
             1 => 'Jan',  2 => 'Feb',  3 => 'Mar',
             4 => 'Apr',  5 => 'Mei',  6 => 'Jun',
             7 => 'Jul',  8 => 'Agt',  9 => 'Sep',
            10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
    }

    public static function getNamaBulan(int $bulan): string
    {
        return self::getBulanList()[$bulan] ?? '-';
    }

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