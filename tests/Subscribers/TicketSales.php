<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;

class TicketSales
{
    public function scope(Model $baseReportModel, Model|string $eventModel = null): mixed
    {
        return $baseReportModel->ticketSales;
    }

    public function include(TicketSale|Model $model): bool
    {
        return true;
    }
}
