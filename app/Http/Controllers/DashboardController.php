<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekapBulananTanam;
use App\Models\RekapBulananPanen;
use App\Models\Kabupaten;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistik dasar
        $totalKabupaten = Kabupaten::count();
        
        // 🔥 Perbaikan: Langsung hitung luas total bukan count baris
        $totalLuasTanam = RekapBulananTanam::where('tahun', date('Y'))->sum('total');
        $totalLuasPanen = RekapBulananPanen::where('tahun', date('Y'))->sum('total');

        // Jika tidak ada kolom produksi, atur ke 0 saja
        if (RekapBulananPanen::first() && isset(RekapBulananPanen::first()->produksi)) {
            $totalProduksi = RekapBulananPanen::where('tahun', date('Y'))->sum('produksi');
        } else {
            $totalProduksi = 0;
        }

        // Variabel lama tetap ada agar blade tidak pecah
        $totalRekapTanam = $totalLuasTanam;
        $totalRekapPanen = $totalLuasPanen;
        
        // Hitung total user tergantung level admin
        $totalUsers = 0;
        if ($user->isMasterAdmin()) {
            $totalUsers = User::where('role_id', '!=', 1)->count();
        } elseif ($user->isAdmin()) {
            $totalUsers = User::where('role_id', 3)->count();
        }
        
        // Data terbaru
        $recentTanam = RekapBulananTanam::with('kabupaten')
            ->where('tahun', date('Y'))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentPanen = RekapBulananPanen::with('kabupaten')
            ->where('tahun', date('Y'))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
        // ----- DATA CHART TAHUN INI -----
        $tanamSum = RekapBulananTanam::where('tahun', date('Y'))
            ->selectRaw('SUM(januari) as jan, SUM(februari) as feb, SUM(maret) as mar, SUM(april) as apr, SUM(mei) as mei, SUM(juni) as jun, SUM(juli) as jul, SUM(agustus) as ags, SUM(september) as sep, SUM(oktober) as okt, SUM(november) as nov, SUM(desember) as des')
            ->first();

        $panenSum = RekapBulananPanen::where('tahun', date('Y'))
            ->selectRaw('SUM(januari) as jan, SUM(februari) as feb, SUM(maret) as mar, SUM(april) as apr, SUM(mei) as mei, SUM(juni) as jun, SUM(juli) as jul, SUM(agustus) as ags, SUM(september) as sep, SUM(oktober) as okt, SUM(november) as nov, SUM(desember) as des')
            ->first();
            
        $chartBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $cols = ['jan','feb','mar','apr','mei','jun','jul','ags','sep','okt','nov','des'];
        $chartTanam = [];
        $chartPanen = [];
        foreach ($cols as $col) {
            $chartTanam[] = round($tanamSum->$col ?? 0, 2);
            $chartPanen[] = round($panenSum->$col ?? 0, 2);
        }
        
        return view('dashboard', compact(
            'totalKabupaten', 
            'totalRekapTanam', 
            'totalRekapPanen',
            'totalUsers',
            'totalLuasTanam',
            'totalLuasPanen',
            'totalProduksi',
            'recentTanam',
            'recentPanen',
            'chartBulan',
            'chartTanam',
            'chartPanen'
        ));
    }
}
