<?php

namespace NeonBang\LaravelCrudSourcing\Subscriptions;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Jobs\QueueSubscriber;

class BaseSubscription
{
    protected mixed $metadata = null;

    protected ?Model $record = null;

    public function __construct(protected ?string $attribute = null)
    {
    }

    public function calculate(): void
    {
        // It must be a validated/verified Service Log entry
        if (! $this->validate()) {
            return;
        }

        $currentMetadataValue = $this->record->getMeta($this->key());

        if ($currentMetadataValue && $this->conditions($currentMetadataValue)) {
            $this->metadata = $currentMetadataValue;
        } else {
            if ($currentMetadataValue) {
                $this->record->updateMeta($this->key(), $this->metadata = $this->value());
            } else {
                $this->record->setMeta($this->key(), $this->metadata = $this->value());
            }
        }
    }

    public function key(): string
    {
        return $this->attribute;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function run(mixed $model): void
    {
        $this->record = $model;

        QueueSubscriber::dispatch($this);
    }
}
