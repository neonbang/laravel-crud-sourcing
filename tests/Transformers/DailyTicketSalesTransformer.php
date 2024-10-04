<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Transformers;

class DailyTicketSalesTransformer
{

    public function __invoke($model)
    {
        dump([
            'total_tickets_revenue' => $model->sum('ticket_price'),
            'total_tickets_sold_count' => $model->count(),
        ]);
        return [
            'total_tickets_revenue' => $model->sum('ticket_price'),
            'total_tickets_sold_count' => $model->count(),
        ];
    }
}
