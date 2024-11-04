<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;

class TicketSales
{
    public function scope(TicketSale|Model $model, ?Model $subject = null): mixed
    {
        return $subject->ticketSales;
    }

    public function include(TicketSale|Model $model): bool
    {
        return true;
    }
}
