<?php

namespace App\Modules\Properties\Transformations\Cores;

use Spatie\LaravelData\Data;

class PropertyCoreData extends Data
{
    public function __construct(
        public ?int $agentId = null,
        public ?string $title = null,
        public ?string $price = null,
        public ?string $propertyType = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $description = null,
        public ?string $status = null,
        public ?int $bedrooms = null,
        public ?int $bathrooms = null,
        public ?string $floorArea = null,
        public ?string $lotArea = null,
        public ?int $floors = null,
        public ?string $country = null,
        public ?string $zipCode = null,
        public ?string $latitude = null,
        public ?string $longitude = null,
    ) {}
}
