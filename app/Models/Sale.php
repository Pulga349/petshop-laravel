<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Sale extends Model {
    use HasFactory;
    protected $fillable = ['date', 'client_id', 'total'];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function details(): HasMany { return $this->hasMany(SaleDetail::class); }
}
