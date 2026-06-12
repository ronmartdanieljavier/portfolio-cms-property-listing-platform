<?php

use App\Modules\Properties\Enums\PropertyStatusEnum;
use App\Modules\Properties\Enums\PropertyTypeEnum;
use App\Modules\Properties\Models\PropertyModel;
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

describe('index', function () {
    it('returns a paginated list of properties', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        PropertyModel::factory()->count(3)->create(['agent_id' => $agent->id]);

        $response = $this->getJson('/api/properties', ['Authorization' => "Bearer {$token}"]);

        $response->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonCount(3, 'data');
    });

    it('requires authentication', function () {
        $this->getJson('/api/properties')->assertUnauthorized();
    });
});

describe('show', function () {
    it('returns a single property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id, 'title' => 'Ocean View Villa']);

        $this->getJson("/api/properties/{$property->id}", ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('title', 'Ocean View Villa');
    });

    it('returns 404 for a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->getJson('/api/properties/99999', ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();
    });

    it('requires authentication', function () {
        $this->getJson('/api/properties/1')->assertUnauthorized();
    });
});

describe('update', function () {
    it('updates a property owned by the authenticated agent', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);

        $this->patchJson("/api/properties/{$property->id}", [
            'title' => 'Updated Title',
            'status' => PropertyStatusEnum::Sold->value,
        ], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('title', 'Updated Title')
            ->assertJsonPath('status', PropertyStatusEnum::Sold->value);
    });

    it('returns 403 when updating a property owned by another agent', function () {
        $owner = UserModel::factory()->create();
        $other = UserModel::factory()->create();
        $token = $other->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $owner->id]);

        $this->patchJson("/api/properties/{$property->id}", [
            'title' => 'Hijacked',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertForbidden();
    });

    it('returns 404 when updating a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->patchJson('/api/properties/99999', ['title' => 'Ghost'], ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();
    });

    it('requires authentication', function () {
        $this->patchJson('/api/properties/1', [])->assertUnauthorized();
    });

    it('fails validation with an invalid status', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);

        $this->patchJson("/api/properties/{$property->id}", [
            'status' => 'invalid_status',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    });
});

describe('destroy', function () {
    it('soft-deletes a property owned by the authenticated agent', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);

        $this->deleteJson("/api/properties/{$property->id}", [], ['Authorization' => "Bearer {$token}"])
            ->assertNoContent();

        $this->assertSoftDeleted('properties', ['id' => $property->id]);
    });

    it('returns 403 when deleting a property owned by another agent', function () {
        $owner = UserModel::factory()->create();
        $other = UserModel::factory()->create();
        $token = $other->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $owner->id]);

        $this->deleteJson("/api/properties/{$property->id}", [], ['Authorization' => "Bearer {$token}"])
            ->assertForbidden();
    });

    it('returns 404 when deleting a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->deleteJson('/api/properties/99999', [], ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();
    });

    it('requires authentication', function () {
        $this->deleteJson('/api/properties/1')->assertUnauthorized();
    });
});
