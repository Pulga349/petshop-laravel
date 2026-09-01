<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Product extends Model {
    protected $fillable = ['name', 'sku', 'category', 'description', 'image', 'sale_price', 'purchase_price', 'initial_stock', 'supplier_id'];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }

    public function purchaseDetails(): HasMany { return $this->hasMany(PurchaseDetail::class); }

    public function saleDetails(): HasMany { return $this->hasMany(SaleDetail::class); }

    /**
     * Calcula el stock actual dinámicamente:
     * Stock = initial_stock + SUM(purchase_details.quantity) - SUM(sale_details.quantity)
     */
    public function getStock(): int {
        $initialStock = $this->initial_stock ?? 0;
        $purchases = $this->purchaseDetails()->sum('quantity');
        $sales = $this->saleDetails()->sum('quantity');
        return (int) ($initialStock + $purchases - $sales);
    }
}
