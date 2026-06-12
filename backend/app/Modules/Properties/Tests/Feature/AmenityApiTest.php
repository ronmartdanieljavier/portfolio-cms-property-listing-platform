<?php

use App\Modules\Properties\Models\AmenityModel;
use App\Modules\Users\Models\UserModel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

describe('index', function () {
    it('returns all amenities ordered alphabetically', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        AmenityModel::factory()->create(['name' => 'Swimming Pool']);
        AmenityModel::factory()->create(['name' => 'Gym']);
        AmenityModel::factory()->create(['name' => 'Parking']);

        $response = $this->getJson('/api/amenities', ['Authorization' => "Bearer {$token}"]);

        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Gym')
            ->assertJsonPath('1.name', 'Parking')
            ->assertJsonPath('2.name', 'Swimming Pool');
    });

    it('returns an empty array when no amenities exist', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->getJson('/api/amenities', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonCount(0);
    });

    it('requires authentication', function () {
        $this->getJson('/api/amenities')->assertUnauthorized();
    });
});
