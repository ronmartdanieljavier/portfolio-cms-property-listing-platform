<?php

namespace App\Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Properties\Http\Requests\CreatePropertyRequest;
use App\Modules\Properties\Services\PropertyService;
use App\Modules\Properties\Transformations\Cores\PropertyCoreData;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    public function __construct(private PropertyService $propertyService) {}

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
}
