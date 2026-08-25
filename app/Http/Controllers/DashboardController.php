<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Consultation;
use App\Models\Hospitalization;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailyRevenue = Sale::query()
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total');

        $monthlyRevenue = Sale::query()
            ->where('status', 'completed')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total');

        $revenueByModule = Sale::query()
            ->select('module', DB::raw('SUM(total) as revenue'))
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->groupBy('module')
            ->pluck('revenue', 'module');

        $lowStockCount = Product::query()
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->count();

        $stats = [
            'daily_revenue' => $dailyRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'patients_count' => Patient::count(),
            'consultations_today' => Consultation::whereDate('consulted_at', $today)->count(),
            'low_stock_count' => $lowStockCount,
            'occupied_beds' => Hospitalization::where('status', 'admitted')->count(),
            'sales_today' => Sale::whereDate('created_at', $today)->count(),
        ];

        $recentSales = Sale::with(['patient', 'cashier'])
            ->latest()
            ->limit(8)
            ->get();

        $criticalProducts = Product::query()
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->orderBy('stock_quantity')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'clinic' => ClinicSetting::current(),
            'stats' => $stats,
            'revenueByModule' => $revenueByModule,
            'recentSales' => $recentSales,
            'criticalProducts' => $criticalProducts,
        ]);
    }
}
