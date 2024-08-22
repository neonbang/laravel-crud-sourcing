<?php

namespace NeonBang\LaravelCrudSourcing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NeonBang\LaravelCrudSourcing\LaravelCrudSourcing
 */
class LaravelCrudSourcing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NeonBang\LaravelCrudSourcing\LaravelCrudSourcing::class;
    }
}
