<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Album;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;

class NextAlbum
{
    // public function include(Album|Model $model): bool
    // {
    //     if (! $model->release_date) {
    //         return false;
    //     }
    //
    //     // Simply get and compare albums by release date
    //     $lastAlbum = $model->artist->albums()->orderBy('release_date', 'desc')->first();
    //     if ($lastAlbum && $lastAlbum->release_date->greaterThan($model->release_date)) {
    //         return false;
    //     }
    //
    //     return true;
    // }

    public function scope(Album|Model $model): mixed
    {

        $base = $model instanceof Artist ? $model : $model->artist;
        dump(['NextAlbum', get_class($model), func_get_args()]);
        return $base->albums()->orderBy('release_date', 'desc')->first();
    }
}
