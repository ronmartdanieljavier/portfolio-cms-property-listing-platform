<?php

use App\Modules\Properties\Models\AmenityModel;
use App\Modules\Properties\Models\PropertyModel;
use App\Modules\Users\Models\UserModel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

describe('store (add amenities)', function () {
    it('attaches amenities to a property owned by the authenticated agent', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $amenities = AmenityModel::factory()->count(2)->create();

        $response = $this->postJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => $amenities->pluck('id')->all()],
            ['Authorization' => "Bearer {$token}"]
        );

        $response->assertStatus(201)
            ->assertJsonCount(2);

        foreach ($amenities as $amenity) {
            $this->assertDatabaseHas('amenity_property', [
                'property_id' => $property->id,
                'amenity_id' => $amenity->id,
            ]);
        }
    });

    it('does not duplicate amenities already attached', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $amenity = AmenityModel::factory()->create();
        $property->amenities()->attach($amenity->id);

        $this->postJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => [$amenity->id]],
            ['Authorization' => "Bearer {$token}"]
        )->assertStatus(201);

        $this->assertCount(1, $property->fresh()->amenities);
    });

    it('returns 403 when the property belongs to another agent', function () {
        $owner = UserModel::factory()->create();
        $other = UserModel::factory()->create();
        $token = $other->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $owner->id]);
        $amenity = AmenityModel::factory()->create();

        $this->postJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => [$amenity->id]],
            ['Authorization' => "Bearer {$token}"]
        )->assertForbidden();
    });

    it('returns 404 for a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $amenity = AmenityModel::factory()->create();

        $this->postJson(
            '/api/properties/99999/amenities',
            ['amenityIds' => [$amenity->id]],
            ['Authorization' => "Bearer {$token}"]
        )->assertNotFound();
    });

    it('fails validation when amenityIds is missing', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);

        $this->postJson(
            "/api/properties/{$property->id}/amenities",
            [],
            ['Authorization' => "Bearer {$token}"]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('amenityIds');
    });

    it('fails validation with non-existent amenity IDs', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);

        $this->postJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => [99999]],
            ['Authorization' => "Bearer {$token}"]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('amenityIds.0');
    });

    it('requires authentication', function () {
        $this->postJson('/api/properties/1/amenities', [])->assertUnauthorized();
    });
});

describe('update (sync amenities)', function () {
    it('syncs amenities for a property, replacing existing ones', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $old = AmenityModel::factory()->create();
        $new = AmenityModel::factory()->count(2)->create();
        $property->amenities()->attach($old->id);

        $response = $this->putJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => $new->pluck('id')->all()],
            ['Authorization' => "Bearer {$token}"]
        );

        $response->assertOk()
            ->assertJsonCount(2);

        $this->assertDatabaseMissing('amenity_property', [
            'property_id' => $property->id,
            'amenity_id' => $old->id,
        ]);
    });

    it('clears all amenities when syncing with an empty array', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $amenity = AmenityModel::factory()->create();
        $property->amenities()->attach($amenity->id);

        $this->putJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => []],
            ['Authorization' => "Bearer {$token}"]
        )->assertOk()
            ->assertJsonCount(0);
    });

    it('returns 403 when the property belongs to another agent', function () {
        $owner = UserModel::factory()->create();
        $other = UserModel::factory()->create();
        $token = $other->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $owner->id]);

        $this->putJson(
            "/api/properties/{$property->id}/amenities",
            ['amenityIds' => []],
            ['Authorization' => "Bearer {$token}"]
        )->assertForbidden();
    });

    it('returns 404 for a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->putJson(
            '/api/properties/99999/amenities',
            ['amenityIds' => []],
            ['Authorization' => "Bearer {$token}"]
        )->assertNotFound();
    });

    it('requires authentication', function () {
        $this->putJson('/api/properties/1/amenities', [])->assertUnauthorized();
    });
});

describe('destroy (remove amenity)', function () {
    it('detaches a single amenity from a property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $amenity = AmenityModel::factory()->create();
        $property->amenities()->attach($amenity->id);

        $this->deleteJson(
            "/api/properties/{$property->id}/amenities/{$amenity->id}",
            [],
            ['Authorization' => "Bearer {$token}"]
        )->assertNoContent();

        $this->assertDatabaseMissing('amenity_property', [
            'property_id' => $property->id,
            'amenity_id' => $amenity->id,
        ]);
    });

    it('returns 404 when the amenity is not attached to the property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $agent->id]);
        $amenity = AmenityModel::factory()->create();

        $this->deleteJson(
            "/api/properties/{$property->id}/amenities/{$amenity->id}",
            [],
            ['Authorization' => "Bearer {$token}"]
        )->assertNotFound();
    });

    it('returns 403 when the property belongs to another agent', function () {
        $owner = UserModel::factory()->create();
        $other = UserModel::factory()->create();
        $token = $other->createToken('api-token')->plainTextToken;
        $property = PropertyModel::factory()->create(['agent_id' => $owner->id]);
        $amenity = AmenityModel::factory()->create();
        $property->amenities()->attach($amenity->id);

        $this->deleteJson(
            "/api/properties/{$property->id}/amenities/{$amenity->id}",
            [],
            ['Authorization' => "Bearer {$token}"]
        )->assertForbidden();
    });

    it('returns 404 for a non-existent property', function () {
        $agent = UserModel::factory()->create();
        $token = $agent->createToken('api-token')->plainTextToken;

        $this->deleteJson(
            '/api/properties/99999/amenities/1',
            [],
            ['Authorization' => "Bearer {$token}"]
        )->assertNotFound();
    });

    it('requires authentication', function () {
        $this->deleteJson('/api/properties/1/amenities/1')->assertUnauthorized();
    });
});
