<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapBulananTanam extends Model
{
    use HasFactory;

    protected $table = 'rekap_bulanan_tanam';

    protected $fillable = [
        'kabupaten_id',
        'tahun',
        'januari', 'februari', 'maret', 'april',
        'mei', 'juni', 'juli', 'agustus',
        'september', 'oktober', 'november', 'desember',
        'total',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tahun'      => 'integer',
        'januari'    => 'decimal:2',
        'februari'   => 'decimal:2',
        'maret'      => 'decimal:2',
        'april'      => 'decimal:2',
        'mei'        => 'decimal:2',
        'juni'       => 'decimal:2',
        'juli'       => 'decimal:2',
        'agustus'    => 'decimal:2',
        'september'  => 'decimal:2',
        'oktober'    => 'decimal:2',
        'november'   => 'decimal:2',
        'desember'   => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Hitung total 12 bulan sebelum disimpan
        static::saving(fn($m) => $m->calculateTotal());

        // ── Setelah disimpan → sync ke RekapTahunanTanam ─────────────────────
        static::saved(function ($m) {
            RekapTahunanTanam::updateOrCreate(
                [
                    'kabupaten_id' => $m->kabupaten_id,
                    'tahun'        => $m->tahun,
                ],
                [
                    'total' => $m->total,
                ]
            );
        });

        // ── Setelah dihapus → hapus RekapTahunanTanam juga ───────────────────
        static::deleted(function ($m) {
            RekapTahunanTanam::where('kabupaten_id', $m->kabupaten_id)
                ->where('tahun', $m->tahun)
                ->delete();
        });
    }

    public function calculateTotal(): void
    {
        $this->total =
            ($this->januari   ?? 0) + ($this->februari  ?? 0) +
            ($this->maret     ?? 0) + ($this->april     ?? 0) +
            ($this->mei       ?? 0) + ($this->juni      ?? 0) +
            ($this->juli      ?? 0) + ($this->agustus   ?? 0) +
            ($this->september ?? 0) + ($this->oktober   ?? 0) +
            ($this->november  ?? 0) + ($this->desember  ?? 0);
    }

    // ── Relasi ──────────────────────────────────────────────────

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Helpers ─────────────────────────────────────────────────

    public static function getBulanList(): array
    {
        return [
            'januari'   => 'Januari',   'februari'  => 'Februari',
            'maret'     => 'Maret',     'april'     => 'April',
            'mei'       => 'Mei',       'juni'      => 'Juni',
            'juli'      => 'Juli',      'agustus'   => 'Agustus',
            'september' => 'September', 'oktober'   => 'Oktober',
            'november'  => 'November',  'desember'  => 'Desember',
        ];
    }

    public static function getBulanName($bulanNumber)
    {
        $map = [
            1 => 'januari',   2 => 'februari', 3 => 'maret',    4 => 'april',
            5 => 'mei',       6 => 'juni',     7 => 'juli',     8 => 'agustus',
            9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember',
        ];
        return $map[$bulanNumber] ?? null;
    }

    public static function getBulanNumber($bulanName)
    {
        $map = [
            'januari' => 1,   'februari' => 2,  'maret'    => 3,  'april'    => 4,
            'mei'     => 5,   'juni'     => 6,  'juli'     => 7,  'agustus'  => 8,
            'september' => 9, 'oktober'  => 10, 'november' => 11, 'desember' => 12,
        ];
        return $map[strtolower($bulanName)] ?? null;
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByKabupaten($query, $kabupatenId)
    {
        return $query->where('kabupaten_id', $kabupatenId);
    }
}