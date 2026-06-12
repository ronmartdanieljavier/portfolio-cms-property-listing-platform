<?php

namespace App\Modules\Properties\Repositories;

use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Properties\Transformations\Repositories\PropertyRepositoryData;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyRepository
{
    public function __construct(
        protected PropertyModel $propertyModel
    ) {}

    /**
     * Return a paginated list of all properties.
     *
     * @return LengthAwarePaginator<int, PropertyRepositoryData>
     */
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->propertyModel
            ->latest()
            ->paginate($perPage)
            ->through(fn (PropertyModel $property) => PropertyRepositoryData::from($property->toArray()));
    }

    /**
     * Find a property by its ID or return null.
     */
    public function findById(int $id): ?PropertyRepositoryData
    {
        $property = $this->propertyModel->find($id);

        if ($property === null) {
            return null;
        }

        return PropertyRepositoryData::from($property->toArray());
    }

    /**
     * Create a new property record and return its data.
     *
     * @param  PropertyRepositoryData  $data  data required for creating a new property
     */
    public function create(PropertyRepositoryData $data): PropertyRepositoryData
    {
        $property = $this->propertyModel->create($data->toDBCreate());

        return PropertyRepositoryData::from($property->toArray());
    }

    /**
     * Update an existing property and return its refreshed data.
     *
     * @param  PropertyRepositoryData  $data  fields to update
     */
    public function update(int $id, PropertyRepositoryData $data): ?PropertyRepositoryData
    {
        $property = $this->propertyModel->find($id);

        if ($property === null) {
            return null;
        }

        $property->update($data->toDBUpdate());

        return PropertyRepositoryData::from($property->fresh()->toArray());
    }

    /**
     * Soft-delete a property by its ID.
     */
    public function delete(int $id): bool
    {
        $property = $this->propertyModel->find($id);

        if ($property === null) {
            return false;
        }

        return (bool) $property->delete();
    }
}
