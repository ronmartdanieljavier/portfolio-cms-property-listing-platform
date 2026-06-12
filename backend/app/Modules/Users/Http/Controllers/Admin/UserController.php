<?php

namespace App\Modules\Users\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Users\Models\UserModel;
use App\Modules\Users\Transformations\Repositories\UserRepositoryData;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Forcing a user to log out from all devices by deleting their API tokens.
     *
     * @param  UserModel  $user  model of the user to log out
     */
    public function forceLogout(UserModel $user): JsonResponse
    {
        $user->tokens()->delete();

        return response()->json(['message' => 'User has been logged out from all devices.'], Response::HTTP_OK);
    }

    /**
     * Toggling user active status
     *
     * @param  UserModel  $user  model of the user to toggle status
     */
    public function toggleStatus(UserModel $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot change status of an admin user.'], Response::HTTP_FORBIDDEN);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return response()->json([
            'message' => $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.',
            'user' => UserRepositoryData::from($user->toArray()),
        ], Response::HTTP_OK);
    }
}
