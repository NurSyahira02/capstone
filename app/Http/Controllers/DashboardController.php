<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $table = 'asnp___ninja_van_at_pengkalan_chepa___dec_24';

        // Jumlah parcel
        $totalParcel = DB::table($table)->count();

        // Jumlah berat
        $totalWeight = DB::table($table)->sum(DB::raw('`Billing Weight`'));
        $avgWeight = DB::table($table)->avg(DB::raw('`Billing Weight`'));

        // ✅ Jumlah parcel yang berstatus DELIVERED
        $delivered = DB::table($table)
            ->where(DB::raw('`Order Granular Status`'), 'LIKE', '%DELIVERED%')
            ->count();

        // Top city
        $cityStats = DB::table($table)
            ->select(DB::raw('`To Billing Zone` as city, COUNT(*) as total'))
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $cityLabels = $cityStats->pluck('city');
        $cityData = $cityStats->pluck('total');

        // Parcel size distribution
        $sizeStats = DB::table($table)
            ->select(DB::raw('`Parcel Size ID` as size, COUNT(*) as total'))
            ->groupBy('size')
            ->get();
        $sizeLabels = $sizeStats->pluck('size');
        $sizeData = $sizeStats->pluck('total');

        // Top customers
        $customerStats = DB::table($table)
            ->select(DB::raw('`Customer Name` as name, COUNT(*) as total'))
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        $customerLabels = $customerStats->pluck('name');
        $customerData = $customerStats->pluck('total');

        // Trend (parcel per day)
        $trend = DB::table($table)
            ->select(DB::raw('DATE(`Delivery Date`) as day, COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        $trendLabels = $trend->pluck('day');
        $trendData = $trend->pluck('total');

        return view('dashboard', compact(
            'totalParcel',
            'totalWeight',
            'avgWeight',
            'delivered', // 👈 tambahkan ni
            'cityLabels',
            'cityData',
            'sizeLabels',
            'sizeData',
            'customerLabels',
            'customerData',
            'trendLabels',
            'trendData'
        ));
    }
}
