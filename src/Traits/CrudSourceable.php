<?php

namespace NeonBang\LaravelCrudSourcing\Traits;

use Throwable;
use Zoha\Metable;

trait CrudSourceable
{
    use Metable;

    public function __get($key)
    {
        if ($this->attributeOfModelMetadataMap($key)) {
            return $this->meta->$key;
        }

        // Needed for Metable inclusion
        if ($key === "meta") {
            return $this->getLoadedMetaItems();
        }

        return parent::__get($key);
    }

    protected function attributeOfModelMetadataMap(mixed $key): bool
    {
        try {
            return isset(config('crud-sourcing.model_metadata_map')[self::class][$key]);
        } catch (Throwable $throwable) {
            return false;
        }
    }
}
