<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Reports;

use NeonBang\LaravelCrudSourcing\Enums\Group;
use NeonBang\LaravelCrudSourcing\Models\Aggregate;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportData;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\DailyTicketSales;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\TicketSales;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\DailyTicketSalesTransformer;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\TicketSalesTransformer;

class DailyReport extends Aggregate
{
    protected $guarded = [];

    protected $table = 'report_daily_data';

    public static function columns(): array
    {
        return [
            ReportGroup::make([
                ReportData::make('ticket_sales')
                    ->action(DailyTicketSales::class)
                    ->transform(DailyTicketSalesTransformer::class),
            ])
                ->by(Group::DAY)
                ->onEloquentEvent(TicketSale::class, 'created'),
        ];
    }
}
