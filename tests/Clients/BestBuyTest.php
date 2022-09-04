<?php

namespace Tests\Clients;

use App\Clients\BestBuy;
use App\Models\Stock;
use Database\Seeders\RetailerWithProductSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * @group api
 */
class BestBuyTest extends TestCase
{
    use RefreshDatabase;

    public function testTrackAProudct(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $stock = \tap(Stock::first())->update([
            'sku' => 6364253,
            'url' => 'https://www.bestbuy.com/site/nitendo-switch-32gb-console-gray-joy-con/6364253.p?skuId=6364253'
        ]);

        try {
            (new BestBuy())->checkAvailability($stock);
        } catch (Exception $e) {
            $this->fail('Failed to track the BestBuy API properly', $e->getMessage());
        }

        // since it's the integration test, it's makeing the real network call to apis. 
        // the response values can be changed. What we are testing here is that we are not breaking the previous behaviour.
        $this->assertTrue(true);
    }

    public function testCreateProperStatusResponse(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        Http::fake(fn () => ['salePrice' => 299.99, 'onlineAvailability' => true]);

        $stockStatus = (new BestBuy())->checkAvailability(Stock::first());

        $this->assertEquals(29999, $stockStatus->price);
        $this->assertTrue($stockStatus->available);
    }
}
