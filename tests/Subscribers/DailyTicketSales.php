<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;

class DailyTicketSales
{
    public function group(TicketSale|Model $model, string $by = 'created_at'): Builder
    {
        return TicketSale::query()->whereDate('created_at', $model->$by);
    }

    public function scope(TicketSale|Model $model, ?Model $subject = null): mixed
    {
        return $subject->ticketSales;
    }

    public function include(TicketSale|Model $model): bool
    {
        return true;
    }
}
