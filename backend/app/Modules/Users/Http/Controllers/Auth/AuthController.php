<?php

namespace App\Modules\Users\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Users\Http\Requests\Auth\LoginRequest;
use App\Modules\Users\Http\Requests\Auth\RegisterRequest;
use App\Modules\Users\Services\AuthService;
use App\Modules\Users\Transformations\Cores\UserCoreData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Register a new user and return the created user data along with an access token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(UserCoreData::from($request->validated()));

        return response()->json($result, Response::HTTP_CREATED);
    }

    /**
     * Log in a user and return an access token and user data.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Log out the current user and invalidate their access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.'], Response::HTTP_OK);
    }
}
