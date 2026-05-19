<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\RekapBulananTanam;
use App\Models\RekapBulananPanen;
use App\Models\RekapBulananProduksi;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        $totalKabupaten  = Kabupaten::count();
        $totalKecamatan  = Kecamatan::count();
        
        // 🔥 Perbaikan: Hitung luas dalam hektar bukan jumlah baris
        $totalRekapTanam = RekapBulananTanam::where('tahun', date('Y'))->sum('total');
        $totalRekapPanen = RekapBulananPanen::where('tahun', date('Y'))->sum('total');

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

        $luasTanamBulanIni = RekapBulananTanam::whereYear('created_at', $now->year)
                                               ->whereMonth('created_at', $now->month)
                                               ->sum('total');

        $luasPanenBulanIni = RekapBulananPanen::whereYear('created_at', $now->year)
                                               ->whereMonth('created_at', $now->month)
                                               ->sum('total');

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

        return view('user.dashboard', compact(
            'totalKabupaten',
            'totalKecamatan',
            'totalRekapTanam',
            'totalRekapPanen',
            'recentTanam',
            'recentPanen',
            'luasTanamBulanIni',
            'luasPanenBulanIni',
            'chartBulan',
            'chartTanam',
            'chartPanen'
        ));
    }
}