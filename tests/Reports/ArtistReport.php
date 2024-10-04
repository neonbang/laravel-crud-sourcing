<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Models\Aggregate;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportData;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\NextAlbum;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\RecordLabel;
use NeonBang\LaravelCrudSourcing\Tests\Transformers\NextAlbumTransformer;

class ArtistReport extends Aggregate
{
    protected $guarded = [];

    protected $table = 'report_artist_aggregate_data';

    public static function columns(): array
    {
        return [
            ReportData::make('record_label')
                ->action(RecordLabel::class)
                ->onEloquentEvent(Artist::class),

            ReportGroup::make([
                ReportData::make('next_album')
                    ->action(NextAlbum::class)
                    ->transform(NextAlbumTransformer::class),
            ])
                ->onEloquentEvent(Album::class),
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
