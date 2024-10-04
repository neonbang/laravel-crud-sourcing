<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;

class ReportGroup
{
    use EloquentEvents;

    public function __construct(protected array $columns)
    {
    }

    public static function make(array $columns): ReportGroup
    {
        return new static($columns);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
