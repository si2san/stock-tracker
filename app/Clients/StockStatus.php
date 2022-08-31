<?php

namespace App\Clients;

class StockStatus
{
    public function __construct(
        public bool $available,
        public string $price
    ) {
    }
}
