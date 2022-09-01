<?php

namespace Tests\Unit;

use App\Clients\ClientException;
use App\Clients\StockStatus;
use Facades\App\Clients\ClientFactory;
use App\Models\Retailer;
use App\Models\Stock;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function testThrowsExceptionIfAClientIsNotFoundWhenTracking(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        Retailer::first()->update(['name' => 'Foo Retailer']);

        $this->expectException(ClientException::class);

        Stock::first()->track();
    }

    public function testUpdateLocalStockStatusAfterBeingTracked(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        ClientFactory::shouldReceive('make->checkAvailability')->andReturn(new StockStatus(true, 9900));

        // track return type is void. type is used to get the value.
        $stock = \tap(Stock::first())->track();

        $this->assertTrue($stock->in_stock);
        $this->assertEquals(9900, $stock->price);
    }
}
