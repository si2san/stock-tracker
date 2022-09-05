<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $casts = ['in_stock' => 'boolean'];

    public function track(): void
    {
        $status = $this->retalier->client()
            ->checkAvailability($this);

        $this->update([
            'in_stock' => $status->available,
            'price' => $status->price
        ]);

        $this->recordHistory();
    }

    public function retalier(): BelongsTo
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }

    private function recordHistory(): void
    {
        $this->history()->create($this->getHistory());
    }

    private function getHistory(): array
    {
        return [
            'price' => $this->price,
            'in_stock' => $this->in_stock,
            'product_id' => $this->product_id,
        ];
    }
}
