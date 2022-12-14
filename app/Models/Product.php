<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function track(): void
    {
        $this->stock->each->track();
    }

    // where in stock, magic method
    // equals to where(stock,true);
    public function inStock(): bool
    {
        return $this->stock()->whereInStock(true)->exists();
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
