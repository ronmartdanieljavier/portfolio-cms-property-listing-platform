<?php

namespace App\Modules\Properties\Transformations\Cores;

use Spatie\LaravelData\Data;

class PropertyCoreData extends Data
{
    public function __construct(
        public int $agentId,
        public string $title,
        public string $price,
        public string $propertyType,
        public string $address,
        public string $city,
        public string $province,
        public ?string $description = null,
        public string $status = 'for_sale',
        public ?int $bedrooms = null,
        public ?int $bathrooms = null,
        public ?string $floorArea = null,
        public ?string $lotArea = null,
        public ?int $floors = null,
        public string $country = 'AU',
        public ?string $zipCode = null,
        public ?string $latitude = null,
        public ?string $longitude = null,
    ) {}
}
