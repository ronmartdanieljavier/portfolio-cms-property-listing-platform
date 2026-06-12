<?php

namespace App\Modules\Users\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Users\Http\Requests\Admin\RegisterRequest;
use App\Modules\Users\Http\Requests\Admin\UpdateUserRequest;
use App\Modules\Users\Models\UserModel;
use App\Modules\Users\Services\AuthService;
use App\Modules\Users\Transformations\Cores\UserCoreData;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Register a new user on behalf of admin.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(UserCoreData::from($request->validated()));

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $result->user,
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a non-admin user's information and/or password.
     *
     * @param  UpdateUserRequest  $request  validated update payload
     * @param  UserModel  $user  model of the user to update
     */
    public function update(UpdateUserRequest $request, UserModel $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot update an admin user.'], Response::HTTP_FORBIDDEN);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh(),
        ], Response::HTTP_OK);
    }

    /**
     * Retrieve all users.
     */
    public function index(): JsonResponse
    {
        $users = UserModel::all();

        return response()->json($users, Response::HTTP_OK);
    }

    /**
     * Forcing a user to log out from all devices by deleting their API tokens.
     *
     * @param  UserModel  $user  model of the user to log out
     */
    public function forceLogout(UserModel $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot force logout an admin user.'], Response::HTTP_FORBIDDEN);
        }

        $user->tokens()->delete();

        return response()->json(['message' => 'User has been logged out from all devices.'], Response::HTTP_OK);
    }

    /**
     * Delete a non-admin user and revoke their tokens.
     *
     * @param  UserModel  $user  model of the user to delete
     */
    public function destroy(UserModel $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json(['message' => 'Cannot delete an admin user.'], Response::HTTP_FORBIDDEN);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.'], Response::HTTP_OK);
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
            'user' => $user->fresh(),
        ], Response::HTTP_OK);
    }
}
