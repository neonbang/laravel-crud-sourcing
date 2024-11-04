<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TicketSales
{
    public function scope(Model $baseReportModel, Model|string $eventModel = null): Model|Collection|null
    {
        return $baseReportModel->ticketSales;
    }
}
