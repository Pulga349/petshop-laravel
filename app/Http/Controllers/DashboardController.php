<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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

        // Stock bajo (productos con stock 0 o negativo)
        $lowStockProducts = Product::with('supplier')
            ->get()
            ->filter(fn($p) => $p->getStock() <= 0)
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

        // ULTIMAS transacciens
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
        $months = [];
        $salesData = [];
        $purchasesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;

            $month = $date->month;
            $year = $date->year;

            $salesData[] = Sale::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('total');

            $purchasesData[] = Purchase::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
                ->sum('purchase_details.subtotal');
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