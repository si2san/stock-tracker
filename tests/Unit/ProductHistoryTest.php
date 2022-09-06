<?php

namespace Tests\Unit;

use Facades\App\Clients\ClientFactory;
use App\Clients\StockStatus;
use App\Models\Product;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductHistoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group productHistory
     */
    public function testRecordsHistoryEachTimeStockIsTracked(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->mockClientRequest($available = true, $price = 29900);

        $product = Product::first();

        $this->assertCount(0, $product->history);

        $product->track();

        $this->assertCount(1, $product->refresh()->history);

        $history = $product->history->first();
        $stock = $product->stock()->first();

        $this->assertEquals($price, $history->price);
        $this->assertEquals($available, $history->in_stock);
        $this->assertEquals($stock->product_id, $history->product_id);
        $this->assertEquals($stock->id, $history->stock_id);
    }
}
