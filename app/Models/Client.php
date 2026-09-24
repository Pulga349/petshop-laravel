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
     * Resolves the tier for a total spent amount using config('client_tiers.thresholds')
     * (single source of truth, shared with ClientController).
     */
    public static function tierFor(float $totalSpent): string
    {
        foreach (config('client_tiers.thresholds', []) as $tier => $threshold) {
            if ($totalSpent >= (float) $threshold) {
                return (string) $tier;
            }
        }

        return 'Bronze';
    }

    /**
     * Recomputes tier and status from total_spent using defined thresholds.
     * Status is 'active' if total_spent > 0, otherwise 'inactive'.
     */
    public function recalculateTier(): void
    {
        $totalSpent = (float) ($this->total_spent ?? 0);

        $this->tier = self::tierFor($totalSpent);

        $this->status = $totalSpent > 0 ? 'active' : 'inactive';
        $this->save();
    }
}
