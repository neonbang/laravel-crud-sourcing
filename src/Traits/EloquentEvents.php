<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

trait EloquentEvents
{
    protected array $eloquentEvents = [];

    public function getEloquentEvents(): array
    {
        return $this->eloquentEvents;
    }

    public function onEloquentEvent(string $className, string $event = 'saved', string $relationalPathToSubject = null, string $type = 'root'): self
    {
        $this->eloquentEvents[] = [
            'model' => $className,
            'event' => $event,
            'trace' => $relationalPathToSubject,
            'type' => $type,
        ];

        return $this;
    }

    public function onRootEvent(string $className, string $event = 'saved'): self
    {
        return $this->onEloquentEvent($className, $event, null, 'root');
    }

    public function onRelationshipEvent(string $className, string $event = 'updated', string $relationalPathToSubject = null): self
    {
        return $this->onEloquentEvent($className, $event, $relationalPathToSubject, 'relationship');
    }
}
