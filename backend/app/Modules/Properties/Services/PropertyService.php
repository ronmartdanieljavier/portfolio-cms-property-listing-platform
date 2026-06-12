<?php

namespace App\Modules\Properties\Services;

use App\Modules\Properties\Repositories\PropertyRepository;
use App\Modules\Properties\Transformations\Cores\PropertyCoreData;
use App\Modules\Properties\Transformations\Repositories\PropertyRepositoryData;

class PropertyService
{
    public function __construct(
        protected PropertyRepository $propertyRepository
    ) {}

    /**
     * Create a new property.
     *
     * @param  PropertyCoreData  $data  data required for creating a new property
     */
    public function create(PropertyCoreData $data): PropertyRepositoryData
    {
        return $this->propertyRepository->create(PropertyRepositoryData::from($data->toArray()));
    }
}
