<?php

namespace App\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class TrimmedStringCast implements Cast
{
    /**
     * Cast trim
     *
     * @template TModel of BaseData inherited parameter
     *
     * @param  DataProperty  $property  inherited parameter
     * @param  mixed  $value  value needs to be trim
     * @param  array<string, mixed>  $properties  inherited parameter
     * @param  CreationContext<TModel>  $context  inherited parameter
     * @return string|null trimmed data
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return trim($value->value);
        }

        return trim((string) $value);
    }
}
