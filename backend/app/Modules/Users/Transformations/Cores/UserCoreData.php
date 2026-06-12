<?php

namespace App\Modules\Users\Transformations\Cores;

use Spatie\LaravelData\Data;

class UserCoreData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
        public bool $isActive = true,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
