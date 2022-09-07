<?php

namespace App\UseCases;

use App\Clients\StockStatus;
use App\Models\History;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ImportantStockUpdate;

class TrackStock
{
    private StockStatus $status;

    public function __construct(private Stock $stock)
    {
    }

    // let consider using this one to remember what the code base is doing.
    public function handle(): void
    {
        $this->checkAvailability();
        $this->notifyUser();
        $this->refreshStock();
        $this->recordToHistory();
    }

    private function checkAvailability(): void
    {
        $this->status = $this->stock->retalier->client()
            ->checkAvailability($this->stock);
    }

    private function notifyUser(): void
    {
        if ($this->isNowInStock()) {
            User::first()->notify(
                new ImportantStockUpdate($this->stock)
            );
        }
    }

    private function refreshStock(): void
    {
        $this->stock->update([
            'in_stock' => $this->status->available,
            'price' => $this->status->price
        ]);
    }

    private function recordToHistory(): void
    {
        History::create($this->getHistory());
    }

    private function isNowInStock(): bool
    {
        return !$this->stock->in_stock && $this->status->available;
    }

    private function getHistory(): array
    {
        return [
            'price' => $this->stock->price,
            'in_stock' => $this->stock->in_stock,
            'product_id' => $this->stock->product_id,
            'stock_id' => $this->stock->id,
        ];
    }
}
