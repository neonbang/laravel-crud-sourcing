<?php

namespace NeonBang\LaravelCrudSourcing\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Aggregate extends Model
{
    protected $guarded = [];

    abstract public static function columns(): array;

    public static function defaultData($model): array
    {
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
}
