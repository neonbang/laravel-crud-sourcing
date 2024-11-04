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
        // if ($model->created_at->format('Y_m_d') === '2024_07_08') {
        //     dump($model->created_at->format('Y_m_d'));
        //     $hmm = TicketSale::query()->whereDate('created_at', $model->$by->format('Y-m-d'));
        //     dump($hmm->count());
        //     dump($hmm->sum('ticket_price'));
        //     dump($hmm->get()->toArray());
        //     dump($hmm->get()->count());
        //     dump($hmm->get()->sum('ticket_price'));
        // };
        return TicketSale::query()->whereDate('created_at', $model->$by->format('Y-m-d'));
    }

    // public function scope(TicketSale|Model $model, ?Model $subject = null): mixed
    // {
    //     return $subject->ticketSales;
    // }

    public function include(TicketSale|Model $model): bool
    {
        return true;
    }
}
