<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $table = 'ninjavan_data';
        
        // 1. Get filter status from request (default to 'all')
        $selectedMonth = $request->get('month', 'all');

        // Create a base query to reuse for all metrics
        $query = DB::table($table);

        // 2. Apply Month Filter if a specific month is picked
        if ($selectedMonth !== 'all') {
            $query->whereMonth('Delivery_Date', $selectedMonth);
        }

        // 3. Filtered Metrics
        $totalParcel = (clone $query)->count();
        $totalWeight = (clone $query)->sum('Original_Weight');
        $avgWeight   = (clone $query)->avg('Original_Weight');
        $delivered   = (clone $query)->where('Order_Granular_Status', 'LIKE', '%DELIVERED%')->count();

        // 4. TOP 3 STATES
        $stateStats = (clone $query)
            ->select(DB::raw('`L1_Name` as state, COUNT(*) as total'))
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(3)
            ->get();
        $stateLabels = $stateStats->pluck('state');
        $stateData = $stateStats->pluck('total');

        // 5. Parcel Size Distribution
        $sizeStats = (clone $query)
            ->select(DB::raw('`Parcel_Size_ID` as size, COUNT(*) as total'))
            ->groupBy('size')
            ->get();
        $sizeLabels = $sizeStats->pluck('size');
        $sizeData = $sizeStats->pluck('total');

        // 6. Dynamic Trend Logic
        if ($selectedMonth !== 'all') {
            // VIEW MONTH: Show daily counts (2023-02-01, 2023-02-02, etc.)
            $trend = (clone $query)
                ->select(DB::raw('DATE(`Delivery_Date`) as label, COUNT(*) as total'))
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } else {
            // VIEW ALL: Show monthly counts (January, February, etc.)
            $trend = DB::table($table)
                ->select(DB::raw('MONTHNAME(`Delivery_Date`) as label, COUNT(*) as total'))
                ->groupBy('label')
                ->orderBy(DB::raw('MONTH(`Delivery_Date`)'))
                ->get();
        }
        $trendLabels = $trend->pluck('label');
        $trendData = $trend->pluck('total');

        // 7. Gender Distribution
        $genderData = (clone $query)
            ->select('Gender', DB::raw('count(*) as count'))
            ->groupBy('Gender')
            ->get();

        return view('dashboard', compact(
            'totalParcel', 'totalWeight', 'avgWeight', 'delivered', 
            'stateLabels', 'stateData', 'sizeLabels', 'sizeData',
            'trendLabels', 'trendData', 'genderData', 'selectedMonth'
        ));
    }
}