<?php

namespace App\Modules\Users\Transformations\Repositories;

use App\Casts\TrimmedStringCast;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class UserRepositoryData extends Data
{
    public function __construct(
        #[WithCast(TrimmedStringCast::class)]
        public string $name,
        #[WithCast(TrimmedStringCast::class)]
        public string $email,
        #[WithCast(TrimmedStringCast::class)]
        public string $role,
        #[WithCast(TrimmedStringCast::class)]
        public string $password = '',
        #[MapInputName('is_active')]
        public bool $isActive = true,
        #[MapInputName('created_at')]
        public ?string $createdAt = null,
        #[MapInputName('updated_at')]
        public ?string $updatedAt = null,
    ) {}

    public function toDBRegister(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}
