<?php

namespace Tests\Feature;

use App\Clients\StockStatus;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ImportantStockUpdate;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TrackCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RetailerWithProductSeeder::class);
    }

    /**
     * @group test
     */
    public function testTracksProductStock(): void
    {
        $this->assertFalse(Product::first()->inStock());

        $this->mockClientRequest();

        $this->artisan('track')
            ->expectsOutput('All Done!');

        $this->assertTrue(Product::first()->inStock());
    }

    /**
     * @group test
     */
    public function testDoesNotNotifyWhenStockRemainsUnavailable(): void
    {
        $this->mockClientRequest(false);

        $this->artisan('track');

        Notification::assertNothingSent();
    }

    /**
     * @group test
     */
    public function testNotifiesTheUserWhenStockIsNowAvailable(): void
    {
        $this->mockClientRequest();

        $this->artisan('track');

        Notification::assertSentTo(User::first(), ImportantStockUpdate::class);
    }
}
