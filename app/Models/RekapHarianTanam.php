<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RekapHarianTanam extends Model
{
    use HasFactory;

    protected $table = 'rekap_harian_tanam';
    
    protected $fillable = [
        'kabupaten_id',
        'kecamatan_id',
        'tanggal',
        
        // Field untuk 31 hari
        'tgl_1', 'tgl_2', 'tgl_3', 'tgl_4', 'tgl_5', 'tgl_6', 'tgl_7', 'tgl_8', 'tgl_9', 'tgl_10',
        'tgl_11', 'tgl_12', 'tgl_13', 'tgl_14', 'tgl_15', 'tgl_16', 'tgl_17', 'tgl_18', 'tgl_19', 'tgl_20',
        'tgl_21', 'tgl_22', 'tgl_23', 'tgl_24', 'tgl_25', 'tgl_26', 'tgl_27', 'tgl_28', 'tgl_29', 'tgl_30', 'tgl_31',
        
        // Field tambahan
        'total_tanam',  // LTT Reguler
        'oplah',
        'gogo',
        'csr',
        'total_ltt',    // Total LTT = total_tanam + oplah + gogo + csr
        'realisasi',
        'target',
        'keterangan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tgl_1' => 'decimal:2',
        'tgl_2' => 'decimal:2',
        'tgl_3' => 'decimal:2',
        'tgl_4' => 'decimal:2',
        'tgl_5' => 'decimal:2',
        'tgl_6' => 'decimal:2',
        'tgl_7' => 'decimal:2',
        'tgl_8' => 'decimal:2',
        'tgl_9' => 'decimal:2',
        'tgl_10' => 'decimal:2',
        'tgl_11' => 'decimal:2',
        'tgl_12' => 'decimal:2',
        'tgl_13' => 'decimal:2',
        'tgl_14' => 'decimal:2',
        'tgl_15' => 'decimal:2',
        'tgl_16' => 'decimal:2',
        'tgl_17' => 'decimal:2',
        'tgl_18' => 'decimal:2',
        'tgl_19' => 'decimal:2',
        'tgl_20' => 'decimal:2',
        'tgl_21' => 'decimal:2',
        'tgl_22' => 'decimal:2',
        'tgl_23' => 'decimal:2',
        'tgl_24' => 'decimal:2',
        'tgl_25' => 'decimal:2',
        'tgl_26' => 'decimal:2',
        'tgl_27' => 'decimal:2',
        'tgl_28' => 'decimal:2',
        'tgl_29' => 'decimal:2',
        'tgl_30' => 'decimal:2',
        'tgl_31' => 'decimal:2',
        'total_tanam' => 'decimal:2',
        'oplah' => 'decimal:2',
        'gogo' => 'decimal:2',
        'csr' => 'decimal:2',
        'total_ltt' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'target' => 'decimal:2',
    ];

    /**
     * ✅ MODEL EVENTS - AUTO UPDATE/DELETE REKAP BULANAN
     */
    protected static function booted()
    {
        // AFTER CREATE - Update rekap bulanan
        static::created(function ($rekapHarian) {
            \Log::info('RekapHarianTanam CREATED - updating Rekap Bulanan', [
                'id' => $rekapHarian->id,
                'kabupaten_id' => $rekapHarian->kabupaten_id,
                'tanggal' => $rekapHarian->tanggal,
            ]);
            
            $rekapHarian->updateRekapBulanan();
        });

        // AFTER UPDATE - Update rekap bulanan
        static::updated(function ($rekapHarian) {
            \Log::info('RekapHarianTanam UPDATED - updating Rekap Bulanan', [
                'id' => $rekapHarian->id,
                'kabupaten_id' => $rekapHarian->kabupaten_id,
                'tanggal' => $rekapHarian->tanggal,
            ]);
            
            $rekapHarian->updateRekapBulanan();
            
            // Jika tanggal atau kabupaten berubah, update rekap bulanan lama juga
            if ($rekapHarian->isDirty('tanggal') || $rekapHarian->isDirty('kabupaten_id')) {
                $original = $rekapHarian->getOriginal();
                \Log::info('Date/Kabupaten changed - updating old Rekap Bulanan too', [
                    'old_kabupaten_id' => $original['kabupaten_id'],
                    'old_date' => $original['tanggal'],
                ]);
                
                static::updateRekapBulananFor(
                    $original['kabupaten_id'],
                    \Carbon\Carbon::parse($original['tanggal'])->year
                );
            }
        });

        // AFTER DELETE - Update atau hapus rekap bulanan
        static::deleted(function ($rekapHarian) {
            \Log::info('RekapHarianTanam DELETED - checking if Rekap Bulanan should be deleted', [
                'id' => $rekapHarian->id,
                'kabupaten_id' => $rekapHarian->kabupaten_id,
                'tanggal' => $rekapHarian->tanggal,
            ]);
            
            $tahun = $rekapHarian->tanggal->year;
            $kabupatenId = $rekapHarian->kabupaten_id;
            
            // Cek apakah masih ada data harian lain di tahun yang sama
            $hasOtherData = static::where('kabupaten_id', $kabupatenId)
                ->whereYear('tanggal', $tahun)
                ->exists();
            
            if (!$hasOtherData) {
                // Tidak ada data harian lagi, HAPUS rekap bulanan
                $deleted = RekapBulananTanam::where('kabupaten_id', $kabupatenId)
                    ->where('tahun', $tahun)
                    ->delete();
                
                \Log::info('No more daily data - Rekap Bulanan DELETED', [
                    'deleted_count' => $deleted,
                    'kabupaten_id' => $kabupatenId,
                    'tahun' => $tahun,
                ]);
            } else {
                // Masih ada data harian lain, UPDATE rekap bulanan
                \Log::info('Other daily data exists - updating Rekap Bulanan');
                static::updateRekapBulananFor($kabupatenId, $tahun);
            }
        });
    }

    /**
     * ✅ UPDATE REKAP BULANAN - Dipanggil otomatis oleh event listeners
     */
    public function updateRekapBulanan()
    {
        $tahun = $this->tanggal->year;
        $kabupatenId = $this->kabupaten_id;
        
        return static::updateRekapBulananFor($kabupatenId, $tahun);
    }

    /**
     * ✅ UPDATE REKAP BULANAN UNTUK KABUPATEN DAN TAHUN TERTENTU
     */
    public static function updateRekapBulananFor($kabupatenId, $tahun)
    {
        try {
            \Log::info('Updating Rekap Bulanan', [
                'kabupaten_id' => $kabupatenId,
                'tahun' => $tahun,
            ]);

            // Ambil atau buat rekap bulanan
            $rekapBulanan = RekapBulananTanam::firstOrCreate(
                [
                    'kabupaten_id' => $kabupatenId,
                    'tahun' => $tahun
                ],
                [
                    'created_by' => auth()->id()
                ]
            );

            // Hitung total per bulan dari rekap harian
            $monthlyTotals = static::where('kabupaten_id', $kabupatenId)
                ->whereYear('tanggal', $tahun)
                ->selectRaw('MONTH(tanggal) as bulan, SUM(total_ltt) as total_luas')
                ->groupBy('bulan')
                ->pluck('total_luas', 'bulan');

            \Log::info('Monthly totals calculated', [
                'monthly_totals' => $monthlyTotals->toArray(),
            ]);

            // Array nama bulan
            $bulanNames = [
                1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
                5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
                9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
            ];

            // Siapkan data untuk update
            $dataUpdate = [];
            $grandTotal = 0;

            foreach ($bulanNames as $bulanNum => $bulanName) {
                $total = $monthlyTotals->get($bulanNum, 0);
                $dataUpdate[$bulanName] = $total;
                $grandTotal += $total;
            }

            $dataUpdate['total'] = $grandTotal;
            $dataUpdate['updated_by'] = auth()->id();

            // Update rekap bulanan
            $rekapBulanan->update($dataUpdate);

            \Log::info('Rekap Bulanan updated successfully', [
                'id' => $rekapBulanan->id,
                'grand_total' => $grandTotal,
            ]);

            return $rekapBulanan;

        } catch (\Exception $e) {
            \Log::error('Failed to update Rekap Bulanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }

    /**
     * Accessor untuk bulan
     */
    public function getBulanAttribute()
    {
        return $this->tanggal
            ? Carbon::parse($this->tanggal)->month
            : null;
    }

    /**
     * Accessor untuk tahun
     */
    public function getTahunAttribute()
    {
        return $this->tanggal
            ? Carbon::parse($this->tanggal)->year
            : null;
    }

    /**
     * Relasi ke Kabupaten
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Relasi ke User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}