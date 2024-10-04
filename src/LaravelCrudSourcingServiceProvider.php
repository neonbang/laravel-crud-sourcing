<?php

namespace NeonBang\LaravelCrudSourcing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NeonBang\LaravelCrudSourcing\Commands\LaravelCrudSourcingCommand;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;
use NeonBang\LaravelCrudSourcing\Tests\Models\TicketSale;

class LaravelCrudSourcingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/crud-sourcing.php' => config_path('crud-sourcing.php'),
        ], 'laravel-crud-sourcing-config');

        foreach (config('crud-sourcing.models', []) as $model => $subscriptions) {
            foreach ($subscriptions as $attribute => $subscription) {
                $subscriber = new $subscription($attribute);
                foreach (Arr::wrap($subscriber->getEloquentEvents()) as $event) {
                    Event::listen('eloquent.'.$event.': '.$subscriber->getModelClass(), function ($model) use ($subscriber) {
                        $subscriber->run($model);
                    });
                }
            }
        }

        foreach (config('crud-sourcing.reports', []) as $subscription) {
            foreach ($subscription::columns() as $column) {
                if ($column instanceof ReportGroup) {
                    $reportGroup = $column;
                    foreach ($reportGroup->getColumns() as $reportColumn) {
                        foreach ($reportGroup->getEloquentEvents() as $event) {
                            Event::listen('eloquent.'.$event['event'].': '.$event['model'], function ($model) use ($event, $reportColumn, $subscription) {
                                if (! config('crud-sourcing.bypass', false)) {
                                    if (isset($event['trace'])) {
                                        $pieces = explode('.', $event['trace']);
                                        $base = $model;
                                        foreach ($pieces as $piece) {
                                            /** @var Model $base */
                                            $base = $base->loadMissing($piece)->$piece;
                                        }
                                        $subject = $base;
                                    } else {
                                        $subject = $model;
                                    }
                                    dump($event);
                                    $reportColumn->run($model, $subscription, $subject);
                                }
                            });
                        }

                        // Event::listen('eloquent.'.$reportGroup->getEloquentEvent().': '.$reportGroup->getEloquentClass(), function ($model) use ($reportColumn, $subscription) {
                        //     if (! config('crud-sourcing.bypass', false)) {
                        //         $reportColumn->run($model, $subscription);
                        //     }
                        // });
                    }
                } else {
                    foreach ($column->getEloquentEvents() as $event) {
                        Event::listen('eloquent.'.$event['event'].': '.$event['model'], function ($model) use ($event, $column, $subscription) {
                            if (! config('crud-sourcing.bypass', false)) {
                                if (isset($event['trace'])) {
                                    $pieces = explode('.', $event['trace']);
                                    $base = $model;
                                    foreach ($pieces as $piece) {
                                        /** @var Model $base */
                                        $base = $base->loadMissing($piece)->$piece;
                                    }
                                    $subject = $base;
                                } else {
                                    $subject = $model;
                                }
                                dump($event);
                                $column->run($model, $subscription, $subject);
                            }
                        });
                    }

                    // Event::listen('eloquent.'.$column->getEloquentEvent().': '.$column->getEloquentClass(), function ($model) use ($column, $subscription) {
                    //     if (! config('crud-sourcing.bypass', false)) {
                    //         $column->run($model, $subscription);
                    //     }
                    // });
                }
                // Event::listen('eloquent.'.$event.': '.$subscriber->getModelClass(), function ($model) use ($subscriber) {
                //     $subscriber->run($model);
                // });
            }
        }

        // foreach (config('crud-sourcing.model_metadata_map', []) as $model => $subscriptions) {
        //     foreach ($subscriptions as $attribute => $subscription) {
        //         $subscriber = new $subscription($attribute);
        //         foreach (Arr::wrap($subscriber->getEloquentEvents()) as $event) {
        //             Event::listen('eloquent.'.$event.': '.$subscriber->getModelClass(), function ($model) use ($subscriber) {
        //                 $subscriber->run($model);
        //             });
        //         }
        //     }
        // }
    }
}
