<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuasBakuSawah extends Model
{
    use HasFactory;

    protected $table = 'luas_baku_sawah';

    protected $fillable = [
        'kabupaten_id',
        'tahun',
        'luas_baku_sawah',
        'keterangan',
    ];

    protected $casts = [
        'luas_baku_sawah' => 'decimal:2',
        'tahun' => 'integer',
    ];

    /**
     * Relationship dengan Kabupaten
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /**
     * ✅ MODEL EVENTS - AUTO UPDATE IP WHEN LBS CHANGES
     */
    protected static function boot()
    {
        parent::boot();

        // AFTER CREATE - Update semua IP yang menggunakan LBS ini
        static::created(function ($lbs) {
            \Log::info('LuasBakuSawah CREATED - updating related IP', [
                'kabupaten_id' => $lbs->kabupaten_id,
                'tahun' => $lbs->tahun,
                'luas_baku_sawah' => $lbs->luas_baku_sawah,
            ]);
            
            self::updateRelatedIp($lbs);
        });

        // AFTER UPDATE - Update semua IP yang menggunakan LBS ini
        static::updated(function ($lbs) {
            \Log::info('LuasBakuSawah UPDATED - updating related IP', [
                'kabupaten_id' => $lbs->kabupaten_id,
                'tahun' => $lbs->tahun,
                'luas_baku_sawah' => $lbs->luas_baku_sawah,
            ]);
            
            self::updateRelatedIp($lbs);
            
            // Jika tahun berubah, update IP dari tahun lama juga
            if ($lbs->isDirty('tahun')) {
                $oldYear = $lbs->getOriginal('tahun');
                \Log::info('LBS year changed - updating old year IP too', [
                    'old_year' => $oldYear,
                ]);
                self::updateIpForLbsYear($lbs->kabupaten_id, $oldYear);
            }
            
            // Jika kabupaten berubah, update IP dari kabupaten lama juga
            if ($lbs->isDirty('kabupaten_id')) {
                $oldKabupatenId = $lbs->getOriginal('kabupaten_id');
                \Log::info('LBS kabupaten changed - updating old kabupaten IP too', [
                    'old_kabupaten_id' => $oldKabupatenId,
                ]);
                self::updateIpForLbsYear($oldKabupatenId, $lbs->tahun);
            }
        });

        // AFTER DELETE - Update/Delete semua IP yang menggunakan LBS ini
        static::deleted(function ($lbs) {
            \Log::info('LuasBakuSawah DELETED - updating/deleting related IP', [
                'kabupaten_id' => $lbs->kabupaten_id,
                'tahun' => $lbs->tahun,
            ]);
            
            self::updateRelatedIp($lbs);
        });
    }

    /**
     * ✅ UPDATE SEMUA IP YANG MENGGUNAKAN LBS INI
     * LBS tahun 2018 → digunakan untuk IP 2018-2023
     * LBS tahun 2024 → digunakan untuk IP 2024-2028
     */
    protected static function updateRelatedIp($lbs)
    {
        try {
            // Tentukan range tahun IP yang terpengaruh
            $lbsYear = $lbs->tahun;
            $ipYearStart = $lbsYear;
            $ipYearEnd = $lbsYear + 5; // LBS berlaku untuk 6 tahun (misal: 2018-2023)
            
            \Log::info('Updating IP for year range', [
                'lbs_year' => $lbsYear,
                'ip_year_start' => $ipYearStart,
                'ip_year_end' => $ipYearEnd,
            ]);

            // Update IP untuk setiap tahun dalam range
            for ($year = $ipYearStart; $year <= $ipYearEnd; $year++) {
                self::updateIpForYear($lbs->kabupaten_id, $year);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to update related IP from LBS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * ✅ UPDATE IP UNTUK SPECIFIC KABUPATEN & TAHUN
     */
    protected static function updateIpForYear($kabupatenId, $ipYear)
    {
        try {
            \Log::info('Updating IP for specific year', [
                'kabupaten_id' => $kabupatenId,
                'ip_year' => $ipYear,
            ]);

            // Call IndeksPertanamanPadi::syncFromKsaAndLbs
            if (class_exists('\App\Models\IndeksPertanamanPadi')) {
                \App\Models\IndeksPertanamanPadi::syncFromKsaAndLbs($ipYear, $kabupatenId);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to update IP for year', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * ✅ UPDATE IP UNTUK SEMUA TAHUN YANG MENGGUNAKAN LBS TERTENTU
     */
    protected static function updateIpForLbsYear($kabupatenId, $lbsYear)
    {
        try {
            // LBS tahun N berlaku untuk IP tahun N sampai N+5
            $ipYearStart = $lbsYear;
            $ipYearEnd = $lbsYear + 5;
            
            for ($year = $ipYearStart; $year <= $ipYearEnd; $year++) {
                self::updateIpForYear($kabupatenId, $year);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to update IP for LBS year', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get available years (kelipatan 5 tahun + 4)
     */
    public static function getAvailableYears()
    {
        $years = self::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($years)) {
            // Generate default LBS years: 2018, 2024, 2029, 2034, dst
            $years = [2019, 2024, 2029, 2034, 2039, 2044];
            $currentYear = date('Y');
            $years = array_filter($years, fn($y) => $y >= 2018 && $y <= $currentYear + 10);
        }

        return $years;
    }

    /**
     * Get LBS year untuk tahun IP tertentu
     * 2018-2023 → LBS 2018
     * 2024-2028 → LBS 2024
     * 2029-2033 → LBS 2029
     */
    public static function getLbsYearForIpYear($ipYear)
    {
        if ($ipYear < 2019) {
            return 2019;
        }
        
        // Daftar tahun LBS: 2018, 2024, 2029, 2034, 2039, dst
        $lbsYears = [2019, 2024, 2029, 2034, 2039, 2044, 2049, 2054];
        
        foreach ($lbsYears as $key => $lbsYear) {
            if ($ipYear >= $lbsYear) {
                // Cek apakah tahun IP masih dalam range LBS ini
                $nextLbsYear = $lbsYears[$key + 1] ?? ($lbsYear + 100);
                if ($ipYear < $nextLbsYear) {
                    return $lbsYear;
                }
            }
        }
        
        return end($lbsYears);
    }

    /**
     * Get LBS value untuk kabupaten dan tahun IP tertentu
     */
    public static function getLbsForIp($kabupatenId, $ipYear)
    {
        $lbsYear = self::getLbsYearForIpYear($ipYear);
        
        $lbs = self::where('kabupaten_id', $kabupatenId)
            ->where('tahun', $lbsYear)
            ->first();
        
        return $lbs ? $lbs->luas_baku_sawah : 0;
    }

    /**
     * Scope filters
     */
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