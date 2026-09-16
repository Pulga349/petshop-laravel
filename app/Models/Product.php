<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'category_id', 'category_type', 'description', 'image', 'sale_price', 'purchase_price', 'initial_stock', 'supplier_id'];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }

    public function category(): MorphTo { return $this->morphTo(); }

    public function purchaseDetails(): HasMany { return $this->hasMany(PurchaseDetail::class); }

    public function saleDetails(): HasMany { return $this->hasMany(SaleDetail::class); }

    /**
     * Calcula el stock actual dinámicamente usando subquery optimizada.
     * Stock = initial_stock + SUM(purchase_details.quantity) - SUM(sale_details.quantity)
     */
    public function getStock(): int
    {
        $initialStock = $this->initial_stock ?? 0;

        $purchases = (int) DB::table('purchase_details')
            ->where('product_id', $this->id)
            ->sum('quantity');

        $sales = (int) DB::table('sale_details')
            ->where('product_id', $this->id)
            ->sum('quantity');

        return (int) ($initialStock + $purchases - $sales);
    }

    /**
     * Scope optimizado que une purchase_details y sale_details en una sola query.
     */
    public function scopeWithStock($query)
    {
        return $query->select('products.*')
            ->selectRaw('COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM purchase_details WHERE purchase_details.product_id = products.id), 0) as purchase_quantity')
            ->selectRaw('COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM sale_details WHERE sale_details.product_id = products.id), 0) as sale_quantity')
            ->selectRaw('products.initial_stock + COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM purchase_details WHERE purchase_details.product_id = products.id), 0) - COALESCE((SELECT COALESCE(SUM(quantity), 0) FROM sale_details WHERE sale_details.product_id = products.id), 0) as stock');
    }
}
