<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Total de productos
        $totalProducts = Product::count();

        //Total de proveedores
        $totalSuppliers = Supplier::count();

        //Total de clientes
        $totalClients = Client::count();

        // Stock bajo usando scopeWithStock subquery (O(1))
        $lowStockProducts = Product::withStock()
            ->whereRaw('products.initial_stock + COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM purchase_details WHERE purchase_details.product_id = products.id), 0) - COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM sale_details WHERE sale_details.product_id = products.id), 0) <= 0')
            ->count();

        // TOTALES GENERALES
        // -----------------------------------------
        // Total gastado en compras (histórico)
        $totalSpent = Purchase::join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->sum('purchase_details.subtotal');

        // Total vendido (histórico)
        $totalSold = Sale::sum('total');

        // Ganancia histórica
        $totalProfit = $totalSold - $totalSpent;

        // VENTAS DEL MES ACTUAL
        // -----------------------------------------
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $salesThisMonth = Sale::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total');

        // Compras del mes actual
        $purchasesThisMonth = Purchase::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->sum('purchase_details.subtotal');

        // Ganancia del mes
        $profitThisMonth = $salesThisMonth - $purchasesThisMonth;

        // Previous month comparison for the dashboard KPI cards.
        $previousMonth = now()->subMonth();
        $salesPreviousMonth = Sale::whereMonth('date', $previousMonth->month)
            ->whereYear('date', $previousMonth->year)
            ->sum('total');
        $purchasesPreviousMonth = Purchase::whereMonth('date', $previousMonth->month)
            ->whereYear('date', $previousMonth->year)
            ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->sum('purchase_details.subtotal');
        $profitPreviousMonth = $salesPreviousMonth - $purchasesPreviousMonth;

        $variation = static function (float|int $current, float|int $previous): float {
            if ((float) $previous === 0.0) {
                return $current == 0 ? 0.0 : 100.0;
            }

            return round((($current - $previous) / abs($previous)) * 100, 1);
        };

        $kpiComparisons = [
            'sales' => $variation($salesThisMonth, $salesPreviousMonth),
            'purchases' => $variation($purchasesThisMonth, $purchasesPreviousMonth),
            'profit' => $variation($profitThisMonth, $profitPreviousMonth),
        ];

        // ULTIMAS transacciones
        // -----------------------------------------
        $recentSales = Sale::with(['client', 'details'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPurchases = Purchase::with(['supplier', 'details'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // DATOS PARA GRÁFICO DE TENDENCIAS (rango configurable: 1m / 3m / 12m)
        // -----------------------------------------
        // KPIs above stay fixed to current vs previous month; only the chart is range-scoped.
        $monthsCount = $this->resolveRangeMonths($request);
        $chart = $this->chartPayload($monthsCount);

        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'totalSuppliers' => $totalSuppliers,
            'totalClients' => $totalClients,
            'lowStockProducts' => $lowStockProducts,
            'totalSpent' => $totalSpent,
            'totalSold' => $totalSold,
            'totalProfit' => $totalProfit,
            'salesThisMonth' => $salesThisMonth,
            'purchasesThisMonth' => $purchasesThisMonth,
            'profitThisMonth' => $profitThisMonth,
            'salesPreviousMonth' => $salesPreviousMonth,
            'purchasesPreviousMonth' => $purchasesPreviousMonth,
            'profitPreviousMonth' => $profitPreviousMonth,
            'kpiComparisons' => $kpiComparisons,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            'range' => $chart['range'],
            'rangeLabel' => $chart['rangeLabel'],
            'months' => $chart['labels'],
            'salesData' => $chart['sales'],
            'purchasesData' => $chart['purchases'],
        ]);
    }

    /**
     * JSON payload for the dashboard trend chart (range-scoped, no KPIs).
     */
    public function chartData(Request $request): JsonResponse
    {
        return response()->json($this->chartPayload($this->resolveRangeMonths($request)));
    }

    /**
     * Resolve the whitelisted range query param to a month count (default 12).
     */
    private function resolveRangeMonths(Request $request): int
    {
        $rangeMonths = ['1m' => 1, '3m' => 3, '12m' => 12];

        return $rangeMonths[$request->query('range')] ?? 12;
    }

    /**
     * Build the chart payload: resolved range, label, month labels and monthly series.
     */
    private function chartPayload(int $monthsCount): array
    {
        $rangeMonths = ['1m' => 1, '3m' => 3, '12m' => 12];
        $range = array_search($monthsCount, $rangeMonths, true);
        $rangeLabels = ['1m' => 'Este mes', '3m' => 'Últimos 3 meses', '12m' => 'Últimos 12 meses'];
        $rangeLabel = $rangeLabels[$range];

        // Build month labels from oldest to newest for the chart.
        $labels = [];
        $monthKeys = [];
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->isoFormat('MMM');
            $monthKeys[] = $date->format('Y-m');
        }

        $sales = [];
        $purchases = [];

        $rangeStart = now()->subMonths($monthsCount - 1)->startOfMonth();
        $rangeEnd = now()->endOfMonth();

        // Single GROUP BY query for monthly sales
        $monthlySales = Sale::selectRaw('strftime("%Y", date) as year, strftime("%m", date) as month, SUM(total) as total')
            ->whereBetween('date', [$rangeStart, $rangeEnd])
            ->groupByRaw('strftime("%Y", date), strftime("%m", date)')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // Single GROUP BY query for monthly purchases
        $monthlyPurchases = Purchase::selectRaw('strftime("%Y", purchases.date) as year, strftime("%m", purchases.date) as month, SUM(purchase_details.subtotal) as total')
            ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->whereBetween('purchases.date', [$rangeStart, $rangeEnd])
            ->groupByRaw('strftime("%Y", purchases.date), strftime("%m", purchases.date)')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // Map monthly data to labels
        foreach ($monthKeys as $key) {
            $sales[] = $monthlySales[$key]->total ?? 0;
            $purchases[] = $monthlyPurchases[$key]->total ?? 0;
        }

        return [
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'labels' => $labels,
            'sales' => $sales,
            'purchases' => $purchases,
        ];
    }
}
