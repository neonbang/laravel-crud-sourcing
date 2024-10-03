<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;

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

        $column->insert($report, $column, $model->release_date);
    }
}
