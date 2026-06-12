<?php

namespace App\Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Properties\Http\Requests\CreatePropertyRequest;
use App\Modules\Properties\Http\Requests\UpdatePropertyRequest;
use App\Modules\Properties\Services\PropertyService;
use App\Modules\Properties\Transformations\Cores\PropertyCoreData;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    public function __construct(private PropertyService $propertyService) {}

    /**
     * List all property listings with pagination.
     */
    public function index(): JsonResponse
    {
        $result = $this->propertyService->list();

        return response()->json($result);
    }

    /**
     * Return a single property listing.
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->propertyService->show($id);

        if ($result === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($result);
    }

    /**
     * Create a new property listing for the authenticated agent.
     */
    public function store(CreatePropertyRequest $request): JsonResponse
    {
        $result = $this->propertyService->create(
            PropertyCoreData::from([
                ...$request->validated(),
                'agentId' => $request->user()->id,
            ])
        );

        return response()->json($result, Response::HTTP_CREATED);
    }

    /**
     * Update a property listing owned by the authenticated agent.
     */
    public function update(UpdatePropertyRequest $request, int $id): JsonResponse
    {
        $existing = $this->propertyService->show($id);

        if ($existing === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($existing->agentId !== $request->user()->id) {
            return response()->json(['message' => 'You do not have permission to update this property.'], Response::HTTP_FORBIDDEN);
        }

        $result = $this->propertyService->update($id, PropertyCoreData::from($request->validated()));

        return response()->json($result);
    }

    /**
     * Soft-delete a property listing owned by the authenticated agent.
     */
    public function destroy(int $id): JsonResponse
    {
        $request = request();
        $existing = $this->propertyService->show($id);

        if ($existing === null) {
            return response()->json(['message' => 'Property not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($existing->agentId !== $request->user()->id) {
            return response()->json(['message' => 'You do not have permission to delete this property.'], Response::HTTP_FORBIDDEN);
        }

        $this->propertyService->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
