<?php

namespace App\Models;

use App\UseCases\TrackStock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $casts = ['in_stock' => 'boolean'];

    public function track(): void
    {
        (new TrackStock($this))->handle();
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
