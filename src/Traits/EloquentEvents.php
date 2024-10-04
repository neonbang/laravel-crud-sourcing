<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

trait EloquentEvents
{
    protected ?string $eloquentClass = null;

    protected ?string $eloquentEvent = null;

    public function getEloquentClass(): ?string
    {
        return $this->eloquentClass;
    }

    public function getEloquentEvent(): ?string
    {
        return $this->eloquentEvent;
    }

    public function onEloquentEvent(string $className, string $event = 'saved'): self
    {
        $this->eloquentClass = $className;
        $this->eloquentEvent = $event;

        return $this;
    }
}
