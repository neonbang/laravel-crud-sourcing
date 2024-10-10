<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

trait EloquentEvents
{
    protected array $eloquentEvents = [];

    public function getEloquentEvents(): array
    {
        return $this->eloquentEvents;
    }

    public function onEloquentEvent(string $className, string $event = 'saved', string $relationalPathToSubject = null): self
    {
        $this->eloquentEvents[] = [
            'model' => $className,
            'event' => $event,
            'trace' => $relationalPathToSubject,
        ];

        return $this;
    }
}
