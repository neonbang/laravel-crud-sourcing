<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Subscribers;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Tests\Models\RecordLabel as RecordLabelModel;

class RecordLabel
{
    public function collect($builder, $eventModel = null)
    {
        return $builder;
    }

    public function find(Model $eventWhenRootEventButBaseWhenRebuildingModel)
    {
        return RecordLabelModel::find($eventWhenRootEventButBaseWhenRebuildingModel->record_label_id);
    }

    public function scope(Model $baseReportModel, Model|string $eventModel = null): mixed
    {
        if ($baseReportModel instanceof RecordLabelModel) {
            return $baseReportModel;
        }

        return RecordLabelModel::find($baseReportModel->record_label_id);
    }
}
