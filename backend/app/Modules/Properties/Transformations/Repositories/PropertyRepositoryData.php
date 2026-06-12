<?php

namespace App\Modules\Properties\Transformations\Repositories;

use App\Casts\TrimmedStringCast;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class PropertyRepositoryData extends Data
{
    public function __construct(
        #[MapInputName('agent_id')]
        public int $agentId,
        #[WithCast(TrimmedStringCast::class)]
        public string $title,
        public string $price,
        #[MapInputName('property_type')]
        public string $propertyType,
        #[WithCast(TrimmedStringCast::class)]
        public string $address,
        #[WithCast(TrimmedStringCast::class)]
        public string $city,
        #[WithCast(TrimmedStringCast::class)]
        public string $province,
        #[WithCast(TrimmedStringCast::class)]
        public ?string $description = null,
        public string $status = 'for_sale',
        public ?int $bedrooms = null,
        public ?int $bathrooms = null,
        #[MapInputName('floor_area')]
        public ?string $floorArea = null,
        #[MapInputName('lot_area')]
        public ?string $lotArea = null,
        public ?int $floors = null,
        public string $country = 'AU',
        #[MapInputName('zip_code')]
        public ?string $zipCode = null,
        public ?string $latitude = null,
        public ?string $longitude = null,
        #[MapInputName('created_at')]
        public ?string $createdAt = null,
        #[MapInputName('updated_at')]
        public ?string $updatedAt = null,
    ) {}

    public function toDBCreate(): array
    {
        return [
            'agent_id' => $this->agentId,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'property_type' => $this->propertyType,
            'status' => $this->status,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'floor_area' => $this->floorArea,
            'lot_area' => $this->lotArea,
            'floors' => $this->floors,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'zip_code' => $this->zipCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
