<?php

namespace App\Clients;

use App\Models\Stock;
use Illuminate\Support\Facades\Http;

class BestBuy implements Client
{
    public function checkAvailability(Stock $stock): StockStatus
    {
        $result = Http::get($this->endpoint($stock->sku))->json();

        return new StockStatus(
            $result['onlineAvailability'],
            $result['salePrice'] * 100
        );
    }

    private function endpoint(string $sku): string
    {
        $apiKey = \config('services.clients.bestBuy.key');

        return "https://api.bestbuy.com/v1/products/{$sku}.json?apiKey={$apiKey}";
    }
}
