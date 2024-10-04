<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\Artist;

class RecordLabel
{
    public function __invoke(Artist|Model $model, $report, $column)
    {
        $column->insert($report, $column, \NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel::find($model->record_label_id)->name);
    }

    public function include(Artist|Model $model): bool
    {
        return true;
    }

    public function scope(Artist|Model $model): mixed
    {
        return \NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel::find($model->record_label_id)->name;
    }
}
