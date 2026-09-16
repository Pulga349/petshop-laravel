<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    public function products(): MorphMany { return $this->morphMany(Product::class, 'category'); }

    public function suppliers(): MorphMany { return $this->morphMany(Supplier::class, 'category'); }
}
