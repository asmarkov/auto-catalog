<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generation extends EloquentModel
{
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'name',
        'model_id',
        'id'
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class);
    }

    public function carOffer(): HasMany
    {
        return $this->hasMany(CarOffer::class);
    }
}
