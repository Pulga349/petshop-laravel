<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Client extends Model {
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'address', 'tier', 'status', 'total_spent'];
    public function sales(): HasMany { return $this->hasMany(Sale::class); }

    /**
     * Recomputes tier and status from total_spent using defined thresholds.
     * Bronze < 1000, Silver >= 1000, Gold >= 5000, Platinum >= 10000.
     * Status is 'active' if total_spent > 0, otherwise 'inactive'.
     */
    public function recalculateTier(): void
    {
        $totalSpent = (float) ($this->total_spent ?? 0);

        $this->tier = match(true) {
            $totalSpent >= 10000 => 'Platinum',
            $totalSpent >= 5000 => 'Gold',
            $totalSpent >= 1000 => 'Silver',
            default => 'Bronze',
        };

        $this->status = $totalSpent > 0 ? 'active' : 'inactive';
        $this->save();
    }
}
