<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;
use function Pest\Laravel\instance;

class NextAlbum
{
    public function __invoke(Album|Model $model, $report, $column)
    {
        if (! $model->release_date) {
            return;
        }

        // Simply get and compare albums by release date
        $lastAlbum = $model->artist->albums()->orderBy('release_date', 'desc')->first();
        if ($lastAlbum && $lastAlbum->release_date->greaterThan($model->release_date)) {
            return;
        }

        $column->insert($report, $column, $model);
    }

    public static function subjectModelNormalizer(Model $model)
    {
        return match (get_class($model)) {
            Artist::class => self::scoped($model),
            default => $model,
        };
    }

    public function include(Album|Model $model): bool
    {
        if (! $model->release_date) {
            return false;
        }

        // Simply get and compare albums by release date
        $lastAlbum = $model->artist->albums()->orderBy('release_date', 'desc')->first();
        if ($lastAlbum && $lastAlbum->release_date->greaterThan($model->release_date)) {
            return false;
        }

        return true;
    }

    public function scope(Album|Model $model): mixed
    {
        $base = $model instanceof Artist ? $model : $model->artist;
        return $base->albums()->orderBy('release_date', 'desc')->first();
    }

    public static function scoped(Album|Model $model): mixed
    {
        $base = $model instanceof Artist ? $model : $model->artist;
        return $base->albums()->orderBy('release_date', 'desc')->first();
    }
}
