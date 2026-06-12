<?php

namespace App\Modules\Properties\Repositories;

use App\Modules\Properties\Models\AmenityModel;
use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Properties\Transformations\Repositories\AmenityRepositoryData;
use Illuminate\Support\Collection;

class PropertyAmenityRepository
{
    public function __construct(
        protected PropertyModel $propertyModel
    ) {}

    /**
     * Attach amenities to a property without removing existing ones.
     *
     * @param  array<int>  $amenityIds
     * @return Collection<int, AmenityRepositoryData>
     */
    public function attach(int $propertyId, array $amenityIds): Collection
    {
        $property = $this->propertyModel->findOrFail($propertyId);
        $property->amenities()->syncWithoutDetaching($amenityIds);

        return $property->amenities()->get()
            ->map(fn (AmenityModel $amenity) => AmenityRepositoryData::from($amenity->toArray()));
    }

    /**
     * Sync all amenities for a property, replacing existing ones.
     *
     * @param  array<int>  $amenityIds
     * @return Collection<int, AmenityRepositoryData>
     */
    public function sync(int $propertyId, array $amenityIds): Collection
    {
        $property = $this->propertyModel->findOrFail($propertyId);
        $property->amenities()->sync($amenityIds);

        return $property->amenities()->get()
            ->map(fn (AmenityModel $amenity) => AmenityRepositoryData::from($amenity->toArray()));
    }

    /**
     * Detach a single amenity from a property.
     */
    public function detach(int $propertyId, int $amenityId): bool
    {
        $property = $this->propertyModel->findOrFail($propertyId);

        return (bool) $property->amenities()->detach($amenityId);
    }
}
