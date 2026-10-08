<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $category = $request->query('category');
        $sort = in_array($request->query('sort'), ['name', 'created_at', 'status'], true) ? $request->query('sort') : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $suppliers = Supplier::when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();
        return view('suppliers.index', compact('suppliers', 'search', 'status', 'category', 'sort', 'direction'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('suppliers', 'public');
        }

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado correctamente');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load('products');
        return view('suppliers.show', compact('supplier'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($supplier->logo) {
                Storage::disk('public')->delete($supplier->logo);
            }
            $data['logo'] = $request->file('logo')->store('suppliers', 'public');
        }

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->logo) {
            Storage::disk('public')->delete($supplier->logo);
        }
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente');
    }
}
