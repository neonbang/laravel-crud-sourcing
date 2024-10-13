<?php

namespace NeonBang\LaravelCrudSourcing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;

abstract class Projection extends Model
{
    protected Model $eventModel;

    protected $guarded = [];

    abstract public static function columns(): array;

    public static function defaultData($model, $group = null): array
    {
        if ($group) {
            return [
                'group_type' => static::class,
                'group_by_type' => $group->value,
                'group_by' => $model->created_at->format('Y_m_d'),
            ];
        }

        return [
            static::getOwner() => static::getOwnerValue($model),
        ];
    }

    public static function for(Model $model): static|Model
    {
        return self::query()->where(static::getCompositeKey($model))->first();
    }

    public static function getCompositeKey(Model $baseModel, ?Model $eventModel = null): array
    {
        return [
            'id' => $baseModel->id,
        ];
    }

    public static function run(): Builder
    {
        return self::query();
    }

    public static function getOwner(): string
    {
        return 'id';
    }

    public static function getOwnerValue(Model $model): mixed
    {
        return $model->id;
    }

    public static function rebuildFrom(\DateTimeInterface $since)
    {
        foreach (static::columns() as $column) {
            if ($column instanceof ReportGroup) {
                $reportGroup = $column;
                foreach ($reportGroup->getColumns() as $reportColumn) {
                    foreach ($reportGroup->getEloquentEvents() as $event) {
                        $reportColumn->regroupFrom($event['model'], static::class, $reportGroup->getGroupBy(), $since);
                    }
                }
            } else {
                $column->rebuildFrom($column, static::class);
            }
        }
    }

    public static function rebuildFor(Model $baseReportModel)
    {
        // Remove the entry to rebuild it
        // static::query()->where(static::getOwner(), static::getOwnerValue($baseReportModel))->delete();

        foreach (static::columns() as $column) {
            $column->queueRebuild($baseReportModel, static::class);

            foreach ($column->getEloquentEvents() as $event) {
                //
            }
        }

        // return self::for($baseReportModel);
    }

    public static function recalculateFor(Model $eventModel): void
    {
        foreach (static::columns() as $column) {
            if ($column instanceof ReportGroup) {
                $reportGroup = $column;
                foreach ($reportGroup->getEloquentEvents() as $event) {
                    foreach ($reportGroup->getColumns() as $reportColumn) {
                        if ($reportColumn->getSubjectPath() || isset($event['trace'])) {
                            $pieces = explode('.', $reportColumn->getSubjectPath() ?? $event['trace']);
                            $base = $eventModel;
                            foreach ($pieces as $piece) {
                                /** @var Model $base */
                                $base = $base->loadMissing($piece)->$piece;
                            }
                            $baseReportModel = $base;
                        } else {
                            $baseReportModel = $eventModel;
                        }

                        $reportColumn->rebuildFrom($baseReportModel, static::class, $eventModel);
                    }
                }
            } else {
                foreach ($column->getEloquentEvents() as $event) {
                    if (isset($event['trace'])) {
                        $pieces = explode('.', $event['trace']);
                        $base = $eventModel;
                        foreach ($pieces as $piece) {
                            /** @var Model $base */
                            $base = $base->loadMissing($piece)->$piece;
                        }
                        $baseReportModel = $base;
                    } else {
                        $baseReportModel = $eventModel;
                    }

                    $column->rebuildFrom($baseReportModel, static::class, $eventModel);
                }

            }
        }
    }

    public function setEventModel(Model $model): void
    {
        $this->eventModel = $model;
    }
}
