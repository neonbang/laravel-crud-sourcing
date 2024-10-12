<?php

namespace NeonBang\LaravelCrudSourcing\Aggregates;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Jobs\QueueColumn;
use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;

class BelongsToBase
{
    use EloquentEvents;

    protected ?string $handlerClass = null;

    protected ?string $key = null;
    private bool $rebuilding;
    private mixed $eventModel;
    private mixed $report;
    private mixed $baseModel;
    private string $eventType;

    public function __construct(protected string $identifier)
    {
    }

    public static function make(string $identifier): self
    {
        return new static($identifier);
    }

    public function handle(): void
    {
        $method = match ($this->eventType) {
            'relationship' => 'collect',
            default => 'find'
        };

        $handler = new $this->handlerClass;

        if ($method === 'collect') {
            $handler->$method($this->baseModel, $this->eventModel)->each(function (Model $base) use ($handler) {
                $scope = $handler->find($base);

                if ($this->key) {
                    $this->persistByColumn($base, $this->identifier, $scope->{$this->key});
                }
            });
        } else {
            $scope = $handler->$method($this->eventModel);

            if ($this->key) {
                $this->persistByColumn($this->eventModel, $this->identifier, $scope->{$this->key});
            }
        }


        // $listener = new $this->listenerCallback;
        //
        // $userReport = new $report;
        //
        // if ($this->record instanceof Model || $this->subject instanceof Model) {
        //     $userReport->setEventModel($this->record instanceof Model ? $this->record : $this->subject);
        // }
        //
        // if ($this->record instanceof Model && method_exists($listener, 'include')) {
        //     if (!$listener->include($this->record)) {
        //         return;
        //     }
        // }
        //
        // if ($this->isGrouped()) {
        //     $data = $listener->group($this->record);
        // } else {
        //     $data = $listener->scope($this->subject, $this->record, $this->rebuilding);
        // }
        //
        // $this->insert($userReport, $this, $data);
    }

    public function handler(string $handlerClass): self
    {
        $this->handlerClass = $handlerClass;

        return $this;
    }

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function queue(mixed $report, mixed $eventModel, mixed $baseModelReference, string $eventType, bool $rebuild = false): void
    {
        $this->rebuilding = $rebuild;

        $this->eventModel = $eventModel;
        $this->eventType = $eventType;
        $this->report = $report;
        $this->baseModel = $baseModelReference;

        QueueColumn::dispatch($this);
    }

    protected function persistByColumn(Model $baseModel, string $column, mixed $value): void
    {
        $defaults = $this->report::getCompositeKey($this->eventModel, $baseModel);

        $this->report::query()
            ->updateOrCreate($defaults, [$column => $value]);
    }
}
