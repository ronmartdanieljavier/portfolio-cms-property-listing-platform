<?php

namespace App\Casts;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ConvertIsoToDateFormatCast implements Cast
{
    /**
     * Casting of dates
     *
     * @template TModel of BaseData inherited parameter
     *
     * @param  DataProperty  $property  inherited parameter
     * @param  mixed  $value  date value
     * @param  array<string, mixed>  $properties  array of properties
     * @param  CreationContext<TModel>  $context  inherited parameters
     * @return string cast date
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string
    {
        if ($value === null or $value === '') {
            return '';
        }

        if ($value instanceof CarbonImmutable) {
            return $value->format('d/m/Y');
        }

        return CarbonImmutable::parse($value)->format('d/m/Y');
    }
}
