<?php

namespace App\Modules\Users\Services;

use App\Modules\Users\Models\UserModel;
use App\Modules\Users\Repositories\UserRepository;
use App\Modules\Users\Transformations\Cores\UserCoreData;
use App\Modules\Users\Transformations\Cores\UserLoginCoreData;
use App\Modules\Users\Transformations\Cores\UserRegisteredCoreData;
use App\Modules\Users\Transformations\Repositories\UserRepositoryData;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    /**
     * User registration service
     * @param UserCoreData $data data required for registering a new user, including name, email, password, and role
     * @return UserRegisteredCoreData
     */
    public function register(UserCoreData $data): UserRegisteredCoreData
    {
        return $this->userRepository->register(UserRepositoryData::from($data->toArray()));
    }
    
    /**
     * User login service
     * @param string $email email of the user trying to authenticate
     * @param string $password password of the user trying to authenticate
     * @return UserLoginCoreData
     */
    public function login(string $email, string $password): UserLoginCoreData
    {
        return $this->userRepository->authenticateCredentials($email, $password);
    }

    /**
     * User logout service
     * @param UserModel $user the user to logout
     * @return void
     */
    public function logout(UserModel $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
