<?php

namespace NeonBang\LaravelCrudSourcing\Models\Columns;

class ReportGroup
{
    protected ?string $eloquentClass = null;

    protected ?string $eloquentEvent = null;

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

    public function getEloquentClass(): ?string
    {
        return $this->eloquentClass;
    }

    public function getEloquentEvent(): ?string
    {
        return $this->eloquentEvent;
    }

    public function onEloquentEvent(string $className, string $event = 'saved'): ReportGroup
    {
        $this->eloquentClass = $className;
        $this->eloquentEvent = $event;

        return $this;
    }
}
