<?php

namespace App\Modules\Properties\Services;

use App\Modules\Properties\Repositories\PropertyAmenityRepository;
use App\Modules\Properties\Transformations\Repositories\AmenityRepositoryData;
use Illuminate\Support\Collection;

class PropertyAmenityService
{
    public function __construct(
        protected PropertyAmenityRepository $propertyAmenityRepository
    ) {}

    /**
     * Attach amenities to a property without removing existing ones.
     *
     * @param  array<int>  $amenityIds
     * @return Collection<int, AmenityRepositoryData>
     */
    public function add(int $propertyId, array $amenityIds): Collection
    {
        return $this->propertyAmenityRepository->attach($propertyId, $amenityIds);
    }

    /**
     * Sync all amenities for a property, replacing existing ones.
     *
     * @param  array<int>  $amenityIds
     * @return Collection<int, AmenityRepositoryData>
     */
    public function sync(int $propertyId, array $amenityIds): Collection
    {
        return $this->propertyAmenityRepository->sync($propertyId, $amenityIds);
    }

    /**
     * Detach a single amenity from a property.
     */
    public function remove(int $propertyId, int $amenityId): bool
    {
        return $this->propertyAmenityRepository->detach($propertyId, $amenityId);
    }
}
