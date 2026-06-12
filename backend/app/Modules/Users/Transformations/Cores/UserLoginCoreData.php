<?php

namespace App\Modules\Users\Transformations\Cores;

use Spatie\LaravelData\Data;

class UserLoginCoreData extends Data
{
    public function __construct(
        public UserCoreData $user,
        public string $token
    ) {}
}
