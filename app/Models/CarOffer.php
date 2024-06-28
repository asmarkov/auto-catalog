<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarOffer extends EloquentModel
{
    public $timestamps = false;

    public $incrementing = false;

    protected $casts = [
        'manufacture_year' => 'date:Y',
    ];

    protected $fillable = [
        '*'
    ];
    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class);
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class);
    }

    public function engineType(): BelongsTo
    {
        return $this->belongsTo(EngineType::class);
    }

    public function transmission(): BelongsTo
    {
        return $this->belongsTo(Transmission::class);
    }

    public function gearType(): BelongsTo
    {
        return $this->belongsTo(GearType::class);
    }
}
