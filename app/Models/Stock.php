<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $casts = ['in_stock' => 'boolean'];

    public function track(): void
    {
        if ($this->retalier->name === 'Best Buy') {
            $result =  Http::get('http://foo.test')->json();

            $this->update([
                'in_stock' => $result['available'],
                'price' => $result['price']
            ]);
        }
    }

    public function retalier(): BelongsTo
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }
}
