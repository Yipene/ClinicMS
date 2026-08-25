<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Models\Sale;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CaisseReportController extends Controller
{
    public function daily(): View
    {
        $date = request('date', now()->toDateString());
        $day = Carbon::parse($date);

        $sales = Sale::with(['patient', 'cashier', 'payments'])
            ->where('status', 'completed')
            ->whereDate('created_at', $day)
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $sales->sum('total');
        $byModule = $sales->groupBy('module')->map->sum('total');
        $byPayment = $sales->flatMap->payments
            ->groupBy('method')
            ->map->sum('amount');

        $moduleLabels = $this->moduleLabels();
        $paymentLabels = $this->paymentLabels();

        $clinic = ClinicSetting::current();
        $threshold = $clinic->cash_register_threshold;
        $anomaly = $threshold && $totalRevenue < $threshold;

        return view('caisse.reports.daily', compact(
            'date', 'day', 'sales', 'totalRevenue', 'byModule', 'byPayment',
            'moduleLabels', 'paymentLabels', 'clinic', 'anomaly', 'threshold'
        ));
    }

    public function monthly(): View
    {
        $month = request('month', now()->format('Y-m'));
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $prevStart = $start->copy()->subMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();

        $currentTotal = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $previousTotal = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('total');

        $trend = $previousTotal > 0
            ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1)
            : null;

        $dailyChart = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('created_at::date as day, SUM(total) as revenue')
            ->groupByRaw('created_at::date')
            ->orderByRaw('created_at::date')
            ->get();

        $byModule = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->select('module', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('module')
            ->get();

        $purchaseCost = DB::table('stock_movements')
            ->where('type', 'purchase')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(ABS(quantity) * COALESCE(unit_purchase_price, 0)) as cost')
            ->value('cost') ?? 0;

        $netBenefit = $currentTotal - $purchaseCost;

        return view('caisse.reports.monthly', [
            'month' => $month,
            'start' => $start,
            'currentTotal' => $currentTotal,
            'previousTotal' => $previousTotal,
            'trend' => $trend,
            'dailyChart' => $dailyChart,
            'byModule' => $byModule,
            'purchaseCost' => $purchaseCost,
            'netBenefit' => $netBenefit,
            'moduleLabels' => $this->moduleLabels(),
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function exportDaily(): StreamedResponse
    {
        $date = request('date', now()->toDateString());
        $sales = Sale::with('patient')
            ->where('status', 'completed')
            ->whereDate('created_at', $date)
            ->get();

        return $this->csvExport("recette-{$date}.csv", $sales);
    }

    public function exportMonthly(): StreamedResponse
    {
        $month = request('month', now()->format('Y-m'));
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $sales = Sale::with('patient')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return $this->csvExport("recette-{$month}.csv", $sales);
    }

    public function dailyPdf()
    {
        $date = request('date', now()->toDateString());
        $day = Carbon::parse($date);
        $sales = Sale::with(['patient', 'cashier', 'payments'])
            ->where('status', 'completed')
            ->whereDate('created_at', $day)
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('pdf.daily-report', [
            'clinic' => ClinicSetting::current(),
            'date' => $date,
            'day' => $day,
            'sales' => $sales,
            'totalRevenue' => $sales->sum('total'),
            'byModule' => $sales->groupBy('module')->map->sum('total'),
            'moduleLabels' => $this->moduleLabels(),
        ]);

        return $pdf->download("recette-{$date}.pdf");
    }

    public function monthlyPdf()
    {
        $month = request('month', now()->format('Y-m'));
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $pdf = Pdf::loadView('pdf.monthly-report', [
            'clinic' => ClinicSetting::current(),
            'month' => $month,
            'start' => $start,
            'currentTotal' => Sale::where('status', 'completed')->whereBetween('created_at', [$start, $end])->sum('total'),
            'byModule' => Sale::where('status', 'completed')->whereBetween('created_at', [$start, $end])
                ->select('module', DB::raw('SUM(total) as revenue'))->groupBy('module')->get(),
            'moduleLabels' => $this->moduleLabels(),
        ]);

        return $pdf->download("recette-{$month}.pdf");
    }

    protected function csvExport(string $filename, $sales): StreamedResponse
    {
        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Référence', 'Date', 'Module', 'Patient', 'Sous-total', 'Remise', 'Total'], ';');
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->reference,
                    $sale->created_at->format('d/m/Y H:i'),
                    $sale->module,
                    $sale->patient?->full_name ?? '',
                    $sale->subtotal,
                    $sale->discount,
                    $sale->total,
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function moduleLabels(): array
    {
        return [
            'caisse' => 'Caisse',
            'pharmacie' => 'Pharmacie',
            'consultation' => 'Consultation',
            'examen' => 'Examen',
            'hospitalisation' => 'Hospitalisation',
            'intervention' => 'Intervention',
            'accouchement' => 'Accouchement',
            'bloc' => 'Bloc opératoire',
        ];
    }

    protected function paymentLabels(): array
    {
        return [
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'transfer' => 'Virement',
        ];
    }
}
