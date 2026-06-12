<?php

namespace App\Modules\Users\Repositories;

use App\Modules\Users\Enums\UserRoleEnum;
use App\Modules\Users\Models\UserModel;
use App\Modules\Users\Transformations\Cores\UserCoreData;
use App\Modules\Users\Transformations\Cores\UserLoginCoreData;
use App\Modules\Users\Transformations\Cores\UserRegisteredCoreData;
use App\Modules\Users\Transformations\Repositories\UserRepositoryData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserRepository
{
    public function __construct(
        protected UserModel $userModel
    ) {}

    /**
     * User registration method that creates a new user and returns the registered user data along with an API token.
     * @param  UserRepositoryData  $data  data required for registering a new user, including name, email, password, and role
     */
    public function register(UserRepositoryData $data): UserRegisteredCoreData
    {
        $user = $this->userModel->create($data->toDBRegister());
        $token = $user->createToken('api-token')->plainTextToken;
        $user = UserRepositoryData::from($user);

        return UserRegisteredCoreData::from(['user' => UserCoreData::from([
            ...$user->toArray(),
            'role' => UserRoleEnum::from($user->role)->value,
        ]), 'token' => $token]);
    }

    /**
     * User authentication method that verifies the provided email and password, checks if the account is active, and returns the authenticated user data along with an API token.
     * @param  string  $email  email of the user trying to authenticate
     * @param  string  $password  password of the user trying to authenticate
     */
    public function authenticateCredentials(string $email, string $password): UserLoginCoreData
    {
        $user = UserModel::where('email', $email)->first();
        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        $userData = UserRepositoryData::from($user);
        return UserLoginCoreData::from(['user' => UserCoreData::from([
            ...$userData->toArray(),
            'role' => UserRoleEnum::from($userData->role)->value,
        ]), 'token' => $token]);
    }
}
