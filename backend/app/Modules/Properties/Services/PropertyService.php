<?php

namespace App\Modules\Properties\Services;

use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Properties\Repositories\PropertyRepository;
use App\Modules\Properties\Transformations\Cores\PropertyCoreData;
use App\Modules\Properties\Transformations\Repositories\PropertyRepositoryData;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyService
{
    public function __construct(
        protected PropertyRepository $propertyRepository
    ) {}

    /**
     * Return a paginated list of all properties.
     *
     * @return LengthAwarePaginator<int, PropertyRepositoryData>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->propertyRepository->findAll($perPage);
    }

    /**
     * Find a property by ID or return null.
     */
    public function show(int $id): ?PropertyRepositoryData
    {
        return $this->propertyRepository->findById($id);
    }

    /**
     * Find the raw Eloquent model by ID without loading relations.
     * Use when you need the model instance itself (e.g. to operate on relationships).
     */
    public function showModel(int $id): ?PropertyModel
    {
        return $this->propertyRepository->findModel($id);
    }

    /**
     * Create a new property.
     *
     * @param  PropertyCoreData  $data  data required for creating a new property
     */
    public function create(PropertyCoreData $data): PropertyRepositoryData
    {
        return $this->propertyRepository->create(PropertyRepositoryData::from($data->toArray()));
    }

    /**
     * Update an existing property.
     *
     * @param  PropertyCoreData  $data  fields to update
     */
    public function update(int $id, PropertyCoreData $data): ?PropertyRepositoryData
    {
        return $this->propertyRepository->update($id, PropertyRepositoryData::from($data->toArray()));
    }

    /**
     * Soft-delete a property.
     */
    public function delete(int $id): bool
    {
        return $this->propertyRepository->delete($id);
    }
}
