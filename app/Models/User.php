<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted()
    {
        static::deleting(function ($user) {
            $tables = [
                'rekap_bulanan_tanam', 'rekap_bulanan_panen',
                'rekap_tahunan_tanam', 'rekap_tahunan_panen',
                'ksa_luas_tanam', 'ksa_luas_panen', 'ksa_produksi',
                'ip_padi', 'luas_baku_sawah'
            ];
            foreach ($tables as $table) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by')) {
                    \Illuminate\Support\Facades\DB::table($table)->where('created_by', $user->id)->update(['created_by' => null]);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'updated_by')) {
                    \Illuminate\Support\Facades\DB::table($table)->where('updated_by', $user->id)->update(['updated_by' => null]);
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'kabupaten_id',       // ← tambahan baru
        'is_active',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password_changed_at' => 'datetime',
            'password'            => 'hashed',
            'is_active'           => 'boolean',
        ];
    }

    /* =========================
     |  RELATIONSHIPS
     |=========================*/

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Kabupaten yang menjadi tanggung jawab user ini.
     * Jika null → user tidak dibatasi (bisa akses semua kabupaten).
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /* =========================
     |  ROLE HELPERS
     |=========================*/

    public function isMasterAdmin(): bool
    {
        return $this->role?->name === 'master_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role?->name === 'user';
    }

    /* =========================
     |  PERMISSIONS
     |=========================*/

    public function canManageUsers(): bool
    {
        return $this->isAdmin() || $this->isMasterAdmin();
    }

    public function canManageData(): bool
    {
        return $this->isAdmin() || $this->isMasterAdmin();
    }

    /**
     * Apakah user ini dibatasi hanya ke satu kabupaten?
     *
     * master_admin → TIDAK pernah dibatasi, walau kabupaten_id diisi.
     * admin / user → dibatasi jika kabupaten_id terisi.
     */
    public function isKabupatenRestricted(): bool
    {
        if ($this->isMasterAdmin()) {
            return false;
        }

        return !is_null($this->kabupaten_id);
    }

    /**
     * Apakah user boleh mengakses data kabupaten tertentu?
     */
    public function canAccessKabupaten(int $kabupatenId): bool
    {
        if (!$this->isKabupatenRestricted()) {
            return true; // bebas akses semua
        }

        return (int) $this->kabupaten_id === $kabupatenId;
    }

    /**
     * Apakah user boleh mengelola (tambah/edit/hapus) data kabupaten tertentu?
     */
    public function canManageKabupaten(int $kabupatenId): bool
    {
        return $this->canManageData() && $this->canAccessKabupaten($kabupatenId);
    }

    /**
     * Kembalikan kabupaten_id yang diizinkan, atau null jika bebas semua.
     * Pakai ini untuk filter query di controller.
     *
     * Contoh:
     *   $allowedId = Auth::user()->getAllowedKabupatenId();
     *   $query->when($allowedId, fn($q) => $q->where('kabupaten_id', $allowedId));
     */
    public function getAllowedKabupatenId(): ?int
    {
        return $this->isKabupatenRestricted()
            ? (int) $this->kabupaten_id
            : null;
    }

    /* =========================
     |  ACCESSORS
     |=========================*/

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role?->display_name ?? '-';
    }

    public function getPasswordChangedHumanAttribute(): string
    {
        return $this->password_changed_at
            ? $this->password_changed_at->diffForHumans()
            : 'Belum pernah diubah';
    }

    /**
     * Label kabupaten untuk ditampilkan di UI.
     * Tampil "Semua Kabupaten" jika tidak dibatasi.
     */
    public function getKabupatenLabelAttribute(): string
    {
        return $this->kabupaten?->nama_kabupaten ?? 'Semua Kabupaten';
    }
}