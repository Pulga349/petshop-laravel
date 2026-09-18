<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('supplier')->withStock()->paginate(15);

        // Basic KPIs using scopeWithStock data
        $totalProducts = Product::count();

        // Use scopeWithStock for O(1) KPI computation
        $allProducts = Product::withStock()->get();
        $outOfStockCount = 0;
        $totalInventoryValue = 0;

        foreach ($allProducts as $product) {
            $stock = (int) $product->stock;
            if ($stock <= 0) {
                $outOfStockCount++;
            }
            $totalInventoryValue += ($stock * $product->purchase_price);
        }

        return view('products.index', compact('products', 'totalProducts', 'outOfStockCount', 'totalInventoryValue'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente');
    }

    public function show(Product $product): View
    {
        $product->load('supplier');
        return view('products.show', compact('product'));
    }

    public function create(): View
    {
        $suppliers = \App\Models\Supplier::all();
        return view('products.create', compact('suppliers'));
    }

    public function edit(Product $product): View
    {
        $product->load('supplier');
        $suppliers = \App\Models\Supplier::all();
        return view('products.edit', compact('product', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente');
    }
}