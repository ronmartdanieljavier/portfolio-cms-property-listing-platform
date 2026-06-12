<?php

use App\Casts\ConvertIsoToDateFormatCast;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

afterEach(fn () => Mockery::close());

$stub = function (): array {
    return [
        Mockery::mock(DataProperty::class),
        Mockery::mock(CreationContext::class),
    ];
};

it('returns an empty string for a null value', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new ConvertIsoToDateFormatCast)->cast($property, null, [], $context))->toBe('');
});

it('returns an empty string for an empty string value', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new ConvertIsoToDateFormatCast)->cast($property, '', [], $context))->toBe('');
});

it('formats a CarbonImmutable instance to d/m/Y', function () use ($stub) {
    [$property, $context] = $stub();

    $date = CarbonImmutable::create(2024, 3, 15);

    expect((new ConvertIsoToDateFormatCast)->cast($property, $date, [], $context))->toBe('15/03/2024');
});

it('parses an ISO date string and formats it to d/m/Y', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new ConvertIsoToDateFormatCast)->cast($property, '2024-03-15T00:00:00.000000Z', [], $context))->toBe('15/03/2024');
});

it('parses a plain date string and formats it to d/m/Y', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new ConvertIsoToDateFormatCast)->cast($property, '2024-06-01', [], $context))->toBe('01/06/2024');
});
