<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;
class Sale extends Model {
    use HasFactory;
    protected $fillable = ['date', 'client_id', 'total'];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function details(): HasMany { return $this->hasMany(SaleDetail::class); }

    /**
     * Restores stock by iterating sale details and incrementing
     * each product's initial_stock by the detail quantity.
     */
    public function restoreStock(): void
    {
        foreach ($this->details as $detail) {
            $product = Product::findOrFail($detail->product_id);
            $product->increment('initial_stock', $detail->quantity);
        }
    }
}
