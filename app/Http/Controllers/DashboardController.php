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

class DashboardController extends Controller
{
    public function index(): View
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

        // ULTIMAS transacciones
        // -----------------------------------------
        $recentSales = Sale::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // DATOS PARA GRÁFICO DE TENDENCIAS (últimos 12 meses)
        // -----------------------------------------
        // Build month labels from oldest to newest for the chart.
        $months = [];
        $monthKeys = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            $monthKeys[] = $date->format('Y-m');
        }

        $salesData = [];
        $purchasesData = [];

        // Single GROUP BY query for monthly sales
        $monthlySales = Sale::selectRaw('strftime("%Y", date) as year, strftime("%m", date) as month, SUM(total) as total')
            ->whereBetween('date', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupByRaw('strftime("%Y", date), strftime("%m", date)')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // Single GROUP BY query for monthly purchases
        $monthlyPurchases = Purchase::selectRaw('strftime("%Y", purchases.date) as year, strftime("%m", purchases.date) as month, SUM(purchase_details.subtotal) as total')
            ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->whereBetween('purchases.date', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
            ->groupByRaw('strftime("%Y", purchases.date), strftime("%m", purchases.date)')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // Map monthly data to labels
        foreach ($monthKeys as $key) {
            $salesData[] = $monthlySales[$key]->total ?? 0;
            $purchasesData[] = $monthlyPurchases[$key]->total ?? 0;
        }

        return view('dashboard', compact(
            'totalProducts',
            'totalSuppliers',
            'totalClients',
            'lowStockProducts',
            'totalSpent',
            'totalSold',
            'totalProfit',
            'salesThisMonth',
            'purchasesThisMonth',
            'profitThisMonth',
            'recentSales',
            'recentPurchases',
            'months',
            'salesData',
            'purchasesData'
        ));
    }
}
