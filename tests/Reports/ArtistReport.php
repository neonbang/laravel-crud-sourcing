<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Reports;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Aggregates;
use NeonBang\LaravelCrudSourcing\Models\Projection;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\NextAlbum;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\RecordLabel as RecordLabelSubscriber;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\TicketSales;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\NextAlbumTransformer;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\TicketSalesTransformer;
use NeonBang\LaravelCrudSourcing\Traits\Store;

class ArtistReport extends Projection
{
    use Store;

    protected string $baseModel = Artist::class;

    protected $guarded = [];

    protected $table = 'report_artist_aggregate_data';

    public static function columns(): array
    {
        return [
            Aggregates\BelongsToBase::make('record_label')
                ->handler(RecordLabelSubscriber::class)
                ->onRootEvent(Artist::class, 'saved')
                ->onRelationshipEvent(RecordLabel::class, 'updated', 'artists')
                ->key('name'),

            Aggregates\HasManyFromBase::make('next_album_data')
                ->handler(NextAlbum::class)
                ->transformer(NextAlbumTransformer::class)
                ->onRelationshipEvent(Album::class, 'saved', 'artist'),

            Aggregates\HasManyFromBase::make('ticket_sales')
                ->handler(TicketSales::class)
                ->transformer(TicketSalesTransformer::class)
                ->onRelationshipEvent(TicketSale::class, 'created', 'concert.artist'),

            // ReportGroup::make([
            //     ReportData::make('next_album')
            //         ->subjectPath('artist')
            //         ->action(NextAlbum::class)
            //         ->transform(NextAlbumTransformer::class),
            // ])
            //     ->onEloquentEvent(Album::class),
            //
            // ReportGroup::make([
            //     ReportData::make('ticket_sales')
            //         ->subjectPath('concert.artist')
            //         ->action(TicketSales::class)
            //         ->transform(TicketSalesTransformer::class),
            // ])
            //     ->onEloquentEvent(TicketSale::class, 'created'),
        ];
    }

    public static function getCompositeKey(Model $baseModel, ?Model $eventModel = null): array
    {
        return [
            'artist_id' => $baseModel->id,
        ];
    }

    public static function getOwner(): string
    {
        return 'artist_id';
    }

    public static function getOwnerValue(Model $model): mixed
    {
        // Map these
        return $model instanceof RecordLabel ? $model->id : $model->id;
    }
}
