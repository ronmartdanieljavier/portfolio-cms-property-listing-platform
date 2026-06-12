<?php

use App\Modules\Users\Models\UserModel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

describe('register', function () {
    it('registers a new agent user', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'agent',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token'])
            ->assertJsonPath('user.role', 'agent');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'role' => 'agent']);
    });

    it('registers a new admin user', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.role', 'admin');
    });

    it('fails with invalid role', function () {
        $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'superuser',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('role');
    });

    it('fails with duplicate email', function () {
        UserModel::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'agent',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    });
});

describe('login', function () {
    it('logs in an active user', function () {
        $user = UserModel::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'token']);
    });

    it('rejects wrong credentials', function () {
        $user = UserModel::factory()->create(['password' => bcrypt('secret123')]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    });

    it('rejects inactive user', function () {
        $user = UserModel::factory()->inactive()->create(['password' => bcrypt('secret123')]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    });
});

describe('logout', function () {
    it('logs out authenticated user', function () {
        $user = UserModel::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->deleteJson('/api/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    });

    it('rejects unauthenticated logout', function () {
        $this->deleteJson('/api/auth/logout')->assertUnauthorized();
    });
});
