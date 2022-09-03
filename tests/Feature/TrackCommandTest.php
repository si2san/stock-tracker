<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\RetailerWithProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testTracksProductStock(): void
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->assertFalse(Product::first()->inStock());

        Http::fake(fn () => ['onlineAvailability' => true, 'salePrice' => 29900]);

        $this->artisan('track')
            ->expectsOutput('All Done!');

        $this->assertTrue(Product::first()->inStock());
    }
}
