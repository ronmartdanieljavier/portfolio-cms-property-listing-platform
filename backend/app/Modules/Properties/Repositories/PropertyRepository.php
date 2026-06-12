<?php

namespace App\Modules\Properties\Repositories;

use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Properties\Transformations\Repositories\PropertyRepositoryData;

class PropertyRepository
{
    public function __construct(
        protected PropertyModel $propertyModel
    ) {}

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
}
