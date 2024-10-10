<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Transformers;

class NextAlbumTransformer
{

    public function __invoke($model)
    {
        return [
            'next_album_release_date' => $model->release_date,
            'next_album_title' => $model->title,
        ];
    }
}
