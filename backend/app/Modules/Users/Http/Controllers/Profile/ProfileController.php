<?php

namespace App\Modules\Users\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Modules\Users\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Return the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user(), Response::HTTP_OK);
    }

    /**
     * Update the authenticated user's name, email, and/or password.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ], Response::HTTP_OK);
    }
}
