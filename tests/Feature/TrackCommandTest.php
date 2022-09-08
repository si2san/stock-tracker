<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->artisan('track');
        
        $this->assertTrue(Product::first()->inStock());
    }
}
