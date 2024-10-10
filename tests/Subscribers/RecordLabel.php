<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;

class RecordLabel
{
    public function include(Artist|Model $model): bool
    {
        return true;
    }

    public function scope(Model $subjectModel): mixed
    {
        return \NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel::find($subjectModel->record_label_id);
    }

    public function __call(string $name, array $arguments)
    {
        dd($name, $arguments);
    }
}
