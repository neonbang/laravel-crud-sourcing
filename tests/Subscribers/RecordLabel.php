<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;

class RecordLabel
{
    public function scope(Model $baseReportModel, Model|string $eventModel = null): mixed
    {
        return \NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel::find($baseReportModel->record_label_id);
    }
}
