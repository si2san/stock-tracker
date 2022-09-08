<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class TrackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track all products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        Product::all()
            ->tap(fn (Collection $products) => $this->output->progressStart($products->count()))
            ->each(function (Product $product) {
                $product->track();
                $this->output->progressAdvance();
            });

        $this->showResults();
    }

    private function showResults(): void
    {
        $this->output->progressFinish();

        $data = Product::leftJoin('stock', 'stock.product_id', '=', 'products.id')
            ->get($this->getKeys());

        $this->table(
            \array_map('ucwords', $this->getKeys()),
            $data
        );
    }

    private function getKeys(): array
    {
        return ['name', 'price', 'url', 'in_stock'];
    }
}
