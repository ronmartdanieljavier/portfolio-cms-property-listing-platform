<?php

use App\Modules\Properties\Enums\PropertyStatusEnum;
use App\Modules\Properties\Enums\PropertyTypeEnum;
use App\Modules\Users\Models\UserModel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

describe('store', function () {
    it('creates a property listing as an authenticated agent', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $response = $this->postJson('/api/properties', [
            'title' => 'Beautiful House in Sydney',
            'description' => 'A stunning property with ocean views.',
            'price' => '750000.00',
            'propertyType' => PropertyTypeEnum::House->value,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'address' => '12 Harbour St',
            'city' => 'Sydney',
            'province' => 'New South Wales',
            'country' => 'AU',
            'zipCode' => '2000',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'Beautiful House in Sydney')
            ->assertJsonPath('propertyType', PropertyTypeEnum::House->value)
            ->assertJsonPath('status', PropertyStatusEnum::ForSale->value);

        $this->assertDatabaseHas('properties', [
            'agent_id' => $agent->id,
            'title' => 'Beautiful House in Sydney',
        ]);
    });

    it('defaults status to for_sale when not provided', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $response = $this->postJson('/api/properties', [
            'title' => 'Studio Apartment',
            'price' => '250000.00',
            'propertyType' => PropertyTypeEnum::Apartment->value,
            'address' => '5 Collins St',
            'city' => 'Melbourne',
            'province' => 'Victoria',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201)
            ->assertJsonPath('status', PropertyStatusEnum::ForSale->value);
    });

    it('requires authentication', function () {
        $this->postJson('/api/properties', [])->assertUnauthorized();
    });

    it('fails validation when required fields are missing', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->postJson('/api/properties', [], ['Authorization' => "Bearer {$token}"])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'price', 'propertyType', 'address', 'city', 'province']);
    });

    it('fails with an invalid property type', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->postJson('/api/properties', [
            'title' => 'Test Property',
            'price' => '100000',
            'propertyType' => 'invalid_type',
            'address' => '1 Test St',
            'city' => 'Brisbane',
            'province' => 'Queensland',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('propertyType');
    });
});
