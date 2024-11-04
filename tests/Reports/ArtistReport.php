<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Models\Aggregate;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportData;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\NextAlbum;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\RecordLabel;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\TicketSales;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\NextAlbumTransformer;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\TicketSalesTransformer;

class ArtistReport extends Aggregate
{
    protected $guarded = [];

    protected $table = 'report_artist_aggregate_data';

    public static function columns(): array
    {
        return [
            ReportData::make('record_label')
                ->action(RecordLabel::class)
                ->withValue('name')
                ->onEloquentEvent(Artist::class)
                ->onEloquentEvent(\NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel::class, 'updated', 'record.label'),

            ReportGroup::make([
                ReportData::make('next_album')
                    ->subjectPath('artist')
                    ->action(NextAlbum::class)
                    ->transform(NextAlbumTransformer::class),
            ])
                ->onEloquentEvent(Album::class),

            ReportGroup::make([
                ReportData::make('ticket_sales')
                    ->subjectPath('concert.artist')
                    ->action(TicketSales::class)
                    ->transform(TicketSalesTransformer::class),
            ])
                ->onEloquentEvent(TicketSale::class, 'created'),
        ];
    }

    public static function getOwner(): string
    {
        return 'artist_id';
    }

    public static function getOwnerValue(Model $model): mixed
    {
        return $model->id;
    }
}
