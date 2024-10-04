<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

trait EloquentEvents
{
    protected ?string $eloquentClass = null;

    protected ?string $eloquentEvent = null;

    protected array $eloquentEvents = [];

    public function getEloquentClass(): ?string
    {
        return $this->eloquentClass;
    }

    public function getEloquentEvent(): ?string
    {
        return $this->eloquentEvent;
    }

    public function getEloquentEvents(): array
    {
        return $this->eloquentEvents;
    }

    public function onEloquentEvent(string $className, string $event = 'saved', string $relationalPathToSubject = null): self
    {
        $this->eloquentClass = $className;
        $this->eloquentEvent = $event;

        $this->eloquentEvents[] = [
            'model' => $className,
            'event' => $event,
            'trace' => $relationalPathToSubject,
        ];

        return $this;
    }
}
