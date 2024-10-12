<?php

namespace NeonBang\LaravelCrudSourcing;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('\NeonBang\LaravelCrudSourcing\dedot')) {
    function dedot(string $dotNotation, Model $subjectModel): Model|Collection
    {
        $pieces = explode('.', $dotNotation);
        return array_reduce($pieces, function ($model, $piece) {
            return $model->loadMissing($piece)->$piece;
        }, $subjectModel);
    }
}
