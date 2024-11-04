<?php

namespace NeonBang\LaravelCrudSourcing\Aggregates;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Jobs\QueueColumn;
use NeonBang\LaravelCrudSourcing\Jobs\QueueRebuild;
use NeonBang\LaravelCrudSourcing\Traits\EloquentEvents;

class HasManyFromBase
{
    use EloquentEvents;

    protected ?string $handlerClass = null;

    protected ?string $key = null;
    private bool $rebuilding;
    private mixed $eventModel = null;
    private mixed $report;
    private mixed $baseModel;
    private string $eventType;
    private string $transformerClass;

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
            'relationship' => 'scope',
            default => 'find'
        };

        $handler = new $this->handlerClass;

        $transformer = new $this->transformerClass;

        $raw = $handler->$method($this->baseModel, $this->eventModel);

        if ($raw) {
            $data = $transformer($raw);
            $this->persist($data);
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

    public function queueRebuild(mixed $baseModelReference, mixed $report, mixed $eventModel = null): void
    {
        $this->rebuilding = true;

        $this->report = $report;
        $this->baseModel = $baseModelReference;
        $this->eventModel = $eventModel;

        QueueRebuild::dispatch($this);
    }

    public function rebuild()
    {
        $handler = new $this->handlerClass;

        $transformer = new $this->transformerClass;

        $raw = $handler->scope($this->baseModel, $this->eventModel);

        if ($raw) {
            if (method_exists($handler, 'rebuildLoop')) {
                $handler->rebuildLoop($raw, $transformer, function ($data, $baseModel, $eventModel) {
                    $this->persist($data, $baseModel, $eventModel);
                });
            } else {
                $data = $transformer($raw);
                $this->persist($data);
            }
        }
    }

    public function transformer(string $transformer): self
    {
        $this->transformerClass = $transformer;

        return $this;
    }

    protected function persist(array $data, Model $baseModel = null, Model $eventModel = null): void
    {
        $defaults = $this->report::getCompositeKey($baseModel ?: $this->baseModel, $eventModel ?: $this->eventModel);

        $this->report::query()
            ->updateOrCreate($defaults, $data);
    }

    protected function persistByColumn(string $column, mixed $value, Model $baseModel, Model $eventModel = null): void
    {
        $this->persist([$column => $value]);
    }
}
