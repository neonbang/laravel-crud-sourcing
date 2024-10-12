<?php

namespace NeonBang\LaravelCrudSourcing;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NeonBang\LaravelCrudSourcing\Aggregates\BelongsToBase;
use NeonBang\LaravelCrudSourcing\Aggregates\HasManyFromBase;
use NeonBang\LaravelCrudSourcing\Facades\LaravelCrudSourcing as LaravelCrudSourcingConfig;

class LaravelCrudSourcingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/crud-sourcing.php' => config_path('crud-sourcing.php'),
        ], 'laravel-crud-sourcing-config');

        foreach (config('crud-sourcing.models', []) as $model => $subscriptions) {
            foreach ($subscriptions as $attribute => $subscription) {
                $subscriber = new $subscription($attribute);
                foreach (Arr::wrap($subscriber->getEloquentEvents()) as $event) {
                    Event::listen('eloquent.' . $event . ': ' . $subscriber->getModelClass(), function ($model) use ($subscriber) {
                        $subscriber->run($model);
                    });
                }
            }
        }

        foreach (config('crud-sourcing.reports', []) as $subscription) {
            foreach ($subscription::columns() as $column) {
                if ($column instanceof BelongsToBase || $column instanceof HasManyFromBase) {
                    foreach ($column->getEloquentEvents() as $event) {
                        Event::listen('eloquent.' . $event['event'] . ': ' . $event['model'], function ($eventModel) use ($event, $column, $subscription) {
                            if (LaravelCrudSourcingConfig::isDisabled($subscription)) {
                                return;
                            }

                            $baseModelReference = isset($event['trace'])
                                ? dedot($event['trace'], $eventModel)
                                : $eventModel;

                            $column->queue($subscription, $eventModel, $baseModelReference, $event['type'] ?? 'root');
                        });
                    }
                }
                // if ($column instanceof ReportGroup) {
                //     $reportGroup = $column;
                //     foreach ($reportGroup->getColumns() as $reportColumn) {
                //         foreach ($reportGroup->getEloquentEvents() as $event) {
                //             Event::listen('eloquent.' . $event['event'] . ': ' . $event['model'], function ($eventModel) use ($event, $reportColumn, $reportGroup, $subscription) {
                //                 if (LaravelCrudSourcingConfig::isDisabled($subscription)) {
                //                     return;
                //                 }
                //
                //                 if (!config('crud-sourcing.bypass', false)) {
                //                     if ($reportColumn->getSubjectPath() || isset($event['trace'])) {
                //                         $pieces = explode('.', $reportColumn->getSubjectPath() ?? $event['trace']);
                //                         $base = $eventModel;
                //                         foreach ($pieces as $piece) {
                //                             /** @var Model $base */
                //                             $base = $base->loadMissing($piece)->$piece;
                //                         }
                //                         $baseReportModel = $base;
                //                     } else {
                //                         $baseReportModel = $eventModel;
                //                     }
                //
                //                     if ($reportGroup->isGrouped()) {
                //                         $reportColumn->by($reportGroup->getGroupBy());
                //                     }
                //
                //                     $reportColumn->run($baseReportModel, $subscription, $eventModel);
                //                 }
                //             });
                //         }
                //     }
                // } elseif ($column instanceof ReportData) {
                //     foreach ($column->getEloquentEvents() as $event) {
                //         Event::listen('eloquent.' . $event['event'] . ': ' . $event['model'], function ($model) use ($event, $column, $subscription) {
                //             if (LaravelCrudSourcingConfig::isDisabled($subscription)) {
                //                 return;
                //             }
                //
                //             if (!config('crud-sourcing.bypass', false)) {
                //                 if (isset($event['trace'])) {
                //                     $pieces = explode('.', $event['trace']);
                //                     $base = $model;
                //                     foreach ($pieces as $piece) {
                //                         /** @var Model $base */
                //                         $base = $base->loadMissing($piece)->$piece;
                //                     }
                //                     $subject = $base;
                //                 } else {
                //                     $subject = $model;
                //                 }
                //
                //                 $column->run($model, $subscription, $subject);
                //             }
                //         });
                //     }
                // }
            }
        }
    }

    public function register()
    {
        $this->app->singleton(LaravelCrudSourcingConfig::class, function ($app) {
            return new LaravelCrudSourcingConfig();
        });
    }
}
