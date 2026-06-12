<?php

use App\Casts\TrimmedStringCast;
use App\Modules\Users\Enums\UserRoleEnum;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

afterEach(fn () => Mockery::close());

$stub = function (): array {
    return [
        Mockery::mock(DataProperty::class),
        Mockery::mock(CreationContext::class),
    ];
};

it('returns null for a null value', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, null, [], $context))->toBeNull();
});

it('trims surrounding whitespace from a string', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, '  hello  ', [], $context))->toBe('hello');
});

it('returns an empty string unchanged', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, '', [], $context))->toBe('');
});

it('trims a whitespace-only string to an empty string', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, '   ', [], $context))->toBe('');
});

it('returns a clean string without modification', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, 'hello', [], $context))->toBe('hello');
});

it('extracts and trims the value from a backed enum', function () use ($stub) {
    [$property, $context] = $stub();

    expect((new TrimmedStringCast)->cast($property, UserRoleEnum::Agent, [], $context))->toBe('agent');
});
