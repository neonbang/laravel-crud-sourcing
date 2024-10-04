<?php

namespace NeonBang\LaravelCrudSourcing\Models;

use Illuminate\Database\Eloquent\Model;
use NeonBang\LaravelCrudSourcing\Models\Columns\ReportGroup;

abstract class Aggregate extends Model
{
    protected $guarded = [];

    abstract public static function columns(): array;

    public static function defaultData($model, $path = null): array
    {
        if ($path) {
            $pieces = explode('.', $path);
            $base = $model;
            foreach ($pieces as $piece) {
                $base = $base->$piece;
            }
            $model = $base;
        }

        return [
            static::getOwner() => static::getOwnerValue($model),
        ];
    }

    public static function for(Model $model): static|Model
    {
        return self::query()->where(static::getOwner(), static::getOwnerValue($model))->first();
    }

    public static function getOwner(): string
    {
        return 'id';
    }

    public static function getOwnerValue(Model $model): mixed
    {
        return $model->id;
    }

    public static function rebuildFor(Model $model)
    {
        // Remove the entry to rebuild it
        static::query()->where(static::getOwner(), static::getOwnerValue($model))->delete();

        foreach (static::columns() as $column) {
            if ($column instanceof ReportGroup) {
                $reportGroup = $column;
                foreach ($reportGroup->getColumns() as $reportColumn) {
                    $reportColumn->rebuild($model, static::class);
                }
            } else {
                $column->run($model, static::class);
            }
        }

        return self::query()->where(static::getOwner(), static::getOwnerValue($model))->first();
    }
}
