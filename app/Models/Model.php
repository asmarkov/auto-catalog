<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Model extends EloquentModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'mark_id'
    ];

    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    public function carOffer(): HasMany
    {
        return $this->hasMany(CarOffer::class);
    }

    public function generation(): HasMany
    {
        return $this->hasMany(Generation::class);
    }
}
