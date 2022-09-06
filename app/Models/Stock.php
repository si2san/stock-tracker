<?php

namespace App\Models;

use App\Events\NowInStock;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $casts = ['in_stock' => 'boolean'];

    public function track(Closure $callback = null): void
    {
        $status = $this->retalier->client()
            ->checkAvailability($this);

        if (!$this->in_stock && $status->available) {
            event(new NowInStock($this));
        }

        $this->update([
            'in_stock' => $status->available,
            'price' => $status->price
        ]);

        $callback && $callback($this);
    }

    public function retalier(): BelongsTo
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
