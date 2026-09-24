<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = in_array($request->query('sort'), ['name', 'sale_price', 'purchase_price', 'created_at'], true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $category = $request->query('category');
        $categoryId = is_numeric($category) ? (int) $category : null;
        $categories = Category::orderBy('name')->get();
        $products = Product::with('supplier')
            ->withStock()
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->when($categoryId !== null, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

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

        return view('products.index', compact('products', 'totalProducts', 'outOfStockCount', 'totalInventoryValue', 'search', 'sort', 'direction', 'category', 'categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Legacy string field is not validated anymore; never trust it.
        unset($data['category']);
        // category_type is always resolved server-side from category_id.
        if (array_key_exists('category_id', $data)) {
            $data['category_type'] = $data['category_id'] ? Category::class : null;
        }
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
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('suppliers', 'categories'));
    }

    public function edit(Product $product): View
    {
        $product->load('supplier');
        $suppliers = \App\Models\Supplier::all();
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'suppliers', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        // Legacy string field is not validated anymore; never trust it.
        unset($data['category']);
        // category_type is always resolved server-side from category_id.
        if (array_key_exists('category_id', $data)) {
            $data['category_type'] = $data['category_id'] ? Category::class : null;
        }
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
