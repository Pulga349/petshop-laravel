<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\Setting;
use Illuminate\View\View;

class PosController extends Controller
{
    public function create(): View
    {
        $clients = Client::all();
        // withStock() exposes a stock attribute in one query (fixes the old per-row
        // getStock() N+1); getStock() still works for other views that need it.
        $products = Product::withStock()->with('category')->get();
        $categories = Category::all();
        // Top 12 products by total quantity sold; empty DB yields an empty collection.
        $bestSellers = SaleDetail::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(12)
            ->with('product')
            ->get();
        $settings = [
            'iva_rate' => Setting::get('iva_rate', 21),
            'discount_bronze' => Setting::get('discount_bronze', 0),
            'discount_silver' => Setting::get('discount_silver', 5),
            'discount_gold' => Setting::get('discount_gold', 10),
            'discount_platinum' => Setting::get('discount_platinum', 15),
        ];

        return view('pos.terminal', compact('clients', 'products', 'categories', 'bestSellers', 'settings'));
    }
}
