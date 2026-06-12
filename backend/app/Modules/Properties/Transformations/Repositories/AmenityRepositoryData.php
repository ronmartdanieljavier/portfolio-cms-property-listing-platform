<?php

namespace App\Modules\Properties\Transformations\Repositories;

use App\Casts\TrimmedStringCast;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class AmenityRepositoryData extends Data
{
    public function __construct(
        public ?int $id = null,
        #[WithCast(TrimmedStringCast::class)]
        public ?string $name = null,
        #[MapInputName('created_at')]
        public ?string $createdAt = null,
        #[MapInputName('updated_at')]
        public ?string $updatedAt = null,
    ) {}
}
