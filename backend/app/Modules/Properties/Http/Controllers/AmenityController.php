<?php

namespace App\Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Properties\Models\AmenityModel;
use App\Modules\Properties\Transformations\Repositories\AmenityRepositoryData;
use Illuminate\Http\JsonResponse;

class AmenityController extends Controller
{
    /**
     * Return all amenities ordered alphabetically.
     */
    public function index(): JsonResponse
    {
        $amenities = AmenityModel::orderBy('name')
            ->get()
            ->map(fn (AmenityModel $amenity) => AmenityRepositoryData::from($amenity->toArray()));

        return response()->json($amenities);
    }
}
