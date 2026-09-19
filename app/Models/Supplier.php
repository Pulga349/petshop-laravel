<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_person', 'category', 'category_id', 'category_type', 'logo', 'email', 'phone', 'address', 'status'];

    public function products(): HasMany { return $this->hasMany(Product::class); }

    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }

    public function category(): MorphTo { return $this->morphTo(); }

    public function getCategoryAttribute($value)
    {
        if ($this->category_id && $this->category_type) {
            return $this->relationLoaded('category')
                ? $this->getRelation('category')
                : $this->category()->first();
        }

        return $value;
    }

    public function getCategoryNameAttribute(): ?string
    {
        $category = $this->category;

        return $category instanceof Category ? $category->name : $category;
    }
}
