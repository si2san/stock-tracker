<?php

namespace App\Models;

use App\Clients\Client;
use App\Clients\StockStatus;

class Target implements Client
{
    public function checkAvailability(Stock $stock): StockStatus
    {
    }
}
