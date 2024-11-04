<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;

class NextAlbum
{
    public function for($model)
    {
        dd(get_class($model));
    }

    public function include($model): bool
    {
        if (!$model->release_date) {
            return false;
        }

        // Since we are querying in scope(), we check is just extra work on the database, I think.
        $lastAlbum = $model->artist->albums()->orderBy('release_date', 'desc')->first();
        if ($lastAlbum && $lastAlbum->release_date->greaterThan($model->release_date)) {
            return false;
        }

        return true;
    }

    public function scope(Model $baseModel)
    {
        return $baseModel->albums()->orderBy('release_date', 'desc')->first();
    }

    // public function scope(Album|Model $needsToBeAlbum, $needsToBeArtist = null): mixed
    // {
    //     dump(get_class($needsToBeAlbum), get_class($needsToBeArtist));
    //     $base = $needsToBeAlbum instanceof Artist ? $needsToBeAlbum : $needsToBeAlbum->artist;
    //     dd($base);
    //     // dump(['NextAlbum', ['album' => get_class($needsToBeAlbum)], ['artist' => get_class($needsToBeArtist)]]);
    //     return $base->albums()->orderBy('release_date', 'desc')->first();
    // }
}
