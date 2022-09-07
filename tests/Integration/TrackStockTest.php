<?php

namespace Tests\Integration;

use App\Models\History;
use App\Models\Stock;
use App\Notifications\ImportantStockUpdate;
use App\UseCases\TrackStock;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TrackStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->mockClientRequest();
        $this->seed(RetailerWithProductSeeder::class);
        (new TrackStock(Stock::first()))->handle();
    }

    public function testNotifiesTheUser(): void
    {
        Notification::assertTimesSent(1, ImportantStockUpdate::class);
    }

    public function testRefreshesTheLocalStock(): void
    {
        \tap(Stock::first(), function (Stock $stock) {
            $this->assertEquals(29900, $stock->price);
            $this->assertTrue($stock->in_stock);
        });
    }

    public function testRecordsToHistory(): void
    {
        $this->assertEquals(1, History::count());
    }
}
