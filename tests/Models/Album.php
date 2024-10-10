<?php

namespace NeonBang\LaravelCrudSourcing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Album extends Model
{
    protected $casts = [
        'release_date' => 'date',
    ];

    protected $guarded = [];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
