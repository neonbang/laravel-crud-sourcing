<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concert extends Model
{
    protected $guarded = [];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(TicketSale::class);
    }
}
