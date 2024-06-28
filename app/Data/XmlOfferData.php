<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class XmlOfferData extends Data
{
    public function __construct(
        public int $id,
        public string $mark,
        public string $model,
        public ?string $generation,
        public ?int $year,
        public ?int $run,
        public ?string $color,
        public ?string $body_type,
        public ?string $engine_type,
        public ?string $transmission,
        public ?string $gear_type,
        public ?int $generation_id,
    )
    {
    }
}
