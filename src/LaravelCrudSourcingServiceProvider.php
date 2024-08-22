<?php

namespace NeonBang\LaravelCrudSourcing;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NeonBang\LaravelCrudSourcing\Commands\LaravelCrudSourcingCommand;

class LaravelCrudSourcingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/crud-sourcing.php' => config_path('crud-sourcing.php'),
        ], 'laravel-crud-sourcing-config');

        foreach (config('crud-sourcing.model_metadata_map', []) as $model => $subscriptions) {
            foreach ($subscriptions as $attribute => $subscription) {
                $subscriber = new $subscription($attribute);
                foreach (Arr::wrap($subscriber->getEloquentEvents()) as $event) {
                    Event::listen('eloquent.'.$event.': '.$subscriber->getModelClass(), function ($model) use ($subscriber) {
                        $subscriber->run($model);
                    });
                }
            }
        }
    }
}
