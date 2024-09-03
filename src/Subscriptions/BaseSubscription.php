<?php

namespace NeonBang\LaravelCrudSourcing\Subscriptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use NeonBang\LaravelCrudSourcing\Jobs\QueueSubscriber;

abstract class BaseSubscription
{
    protected mixed $metadata = null;

    protected ?Model $record = null;

    public function __construct(protected ?string $attribute = null)
    {
    }

    abstract public function conditions(mixed $currentMetadataValue = null): bool;

    abstract public function getEloquentEvents(): array;

    abstract public function getModelClass(): string;

    abstract public function getOwner(): Model;

    abstract public function validate(): bool;

    abstract public function value(): mixed;

    public function calculate(): void
    {
        // It must be a validated/verified Service Log entry
        if (! $this->validate()) {
            return;
        }

        $currentMetadataValue = $this->getOwner()->getMeta($this->key());

        if ($currentMetadataValue && ! $this->conditions($currentMetadataValue)) {
            $this->metadata = $currentMetadataValue;
        } else {
            if ($currentMetadataValue) {
                $this->getOwner()->updateMeta($this->key(), $this->metadata = $this->value());
            } else {
                $this->getOwner()->setMeta($this->key(), $this->metadata = $this->value());
            }
        }
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function key(): string
    {
        return $this->attribute;
    }

    public function run(mixed $model): void
    {
        $this->record = $model;

        QueueSubscriber::dispatch($this);
    }
}
