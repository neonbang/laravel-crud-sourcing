<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;

class TicketSales
{
    public static function subjectModelNormalizer(Model $model)
    {
        return match (get_class($model)) {
            Artist::class => self::scoped($model),
            default => $model,
        };
    }

    public static function scoped(TicketSale|Model $model): mixed
    {
        $base = $model instanceof Artist ? $model : $model->concert->artist;
        return $base->ticketSales;
    }

    public function scope(TicketSale|Model $model): mixed
    {
        $base = $model instanceof Artist ? $model : $model->concert->artist;
        return $base->ticketSales;
    }

    public function include(TicketSale|Model $model): bool
    {
        return true;
    }
}
