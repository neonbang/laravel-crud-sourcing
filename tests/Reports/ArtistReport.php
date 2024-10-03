<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportData;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Subscribers\NextAlbum;

class ArtistReport extends Model
{
    protected $guarded = [];

    public static function columns()
    {
        return [
            ReportGroup::make([
                ReportData::make('next_album_release_date')
                    ->action(NextAlbum::class),
            ])
                ->onEloquentEvent(Album::class),
        ];
    }

    public static function defaultData($model): array
    {
        return [
            'artist_id' => $model->artist_id,
        ];
    }

    public static function for(Model $model): self
    {
        return self::query()->where('artist_id', $model->id)->first();
    }

    // public static function getNextAlbumReleaseDateData($value)
    // {
    //     return [
    //         'next_album_release_date' => $value,
    //         'next_album_release_date_formatted' => Carbon::parse($value)->format('Y-m-d'),
    //     ];
    // }

    public function getTable()
    {
        return 'report_artist_aggregate_data';
    }
}
