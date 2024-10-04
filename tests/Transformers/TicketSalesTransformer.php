<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Transformers;

class TicketSalesTransformer
{

    public function __invoke($model)
    {
        dump([get_class($model), $model->count(), $model->sum('ticket_price')]);
        return [
            'total_tickets_revenue' => $model->sum('ticket_price'),
            'total_tickets_sold_count' => $model->count(),
        ];
    }
}
