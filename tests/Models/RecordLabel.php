<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecordLabel extends Model
{
    protected $guarded = [];

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }
}
