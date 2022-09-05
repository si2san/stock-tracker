<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function track(): void
    {
        $this->stock->each->track(fn (Stock $stock) => $this->recordHistory($stock));
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

    public function recordHistory(Stock $stock): void
    {
        $this->history()->create($this->getHistory($stock));
    }

    private function getHistory(Stock $stock): array
    {
        return [
            'price' => $stock->price,
            'in_stock' => $stock->in_stock,
            'stock_id' => $stock->id,
        ];
    }
}
