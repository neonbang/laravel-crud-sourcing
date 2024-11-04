<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSale extends Model
{
    protected $guarded = [];

    public function concert(): BelongsTo
    {
        return $this->belongsTo(Concert::class);
    }
}
