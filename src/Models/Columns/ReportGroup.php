<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;
use NeonBang\LaravelCrudSourcing\Traits\GroupableEvents;

class ReportGroup
{
    use EloquentEvents;
    use GroupableEvents;

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
