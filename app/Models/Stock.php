<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    }

    public function retalier(): BelongsTo
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }
}
