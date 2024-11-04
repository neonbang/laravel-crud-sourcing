<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Artist extends Model
{
    protected $guarded = [];

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function concerts(): HasMany
    {
        return $this->hasMany(Concert::class);
    }

    public function ticketSales(): HasManyThrough
    {
        return $this->hasManyThrough(TicketSale::class, Concert::class);
    }
}
