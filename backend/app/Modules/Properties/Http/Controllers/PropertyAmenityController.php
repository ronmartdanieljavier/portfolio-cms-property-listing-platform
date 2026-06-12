<?php

namespace App\Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Properties\Http\Requests\AddPropertyAmenitiesRequest;
use App\Modules\Properties\Http\Requests\SyncPropertyAmenitiesRequest;
use App\Modules\Properties\Services\PropertyAmenityService;
use App\Modules\Properties\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PropertyAmenityController extends Controller
{
    public function __construct(
        private PropertyService $propertyService,
        private PropertyAmenityService $propertyAmenityService,
    ) {}

    /**
     * Attach amenities to a property without removing existing ones.
     */
    public function store(AddPropertyAmenitiesRequest $request, int $propertyId): JsonResponse
    {
        $property = $this->propertyService->showModel($propertyId);

        if ($property === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($property->agent_id !== $request->user()->id) {
            return response()->json(['message' => 'You do not have permission to update this property.'], Response::HTTP_FORBIDDEN);
        }

        $amenities = $this->propertyAmenityService->add($property, $request->validated('amenityIds'));

        return response()->json($amenities, Response::HTTP_CREATED);
    }

    /**
     * Sync all amenities for a property, replacing existing ones.
     */
    public function update(SyncPropertyAmenitiesRequest $request, int $propertyId): JsonResponse
    {
        $property = $this->propertyService->showModel($propertyId);

        if ($property === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($property->agent_id !== $request->user()->id) {
            return response()->json(['message' => 'You do not have permission to update this property.'], Response::HTTP_FORBIDDEN);
        }

        $amenities = $this->propertyAmenityService->sync($property, $request->validated('amenityIds'));

        return response()->json($amenities);
    }

    /**
     * Detach a single amenity from a property.
     */
    public function destroy(int $propertyId, int $amenityId): JsonResponse
    {
        $request = request();
        $property = $this->propertyService->showModel($propertyId);

        if ($property === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($property->agent_id !== $request->user()->id) {
            return response()->json(['message' => 'You do not have permission to update this property.'], Response::HTTP_FORBIDDEN);
        }

        $detached = $this->propertyAmenityService->remove($property, $amenityId);

        if (! $detached) {
            return response()->json(['message' => 'Amenity not found on this property.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
