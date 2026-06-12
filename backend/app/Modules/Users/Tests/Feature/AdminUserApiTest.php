<?php

use App\Modules\Users\Models\UserModel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

describe('force logout', function () {
    it('allows admin to force logout a user', function () {
        $admin = UserModel::factory()->admin()->create();
        $agent = UserModel::factory()->create();
        $agent->createToken('api-token');
        $agent->createToken('api-token-2');

        Sanctum::actingAs($admin, ['*']);

        $this->deleteJson("/api/admin/users/{$agent->id}/force-logout")
            ->assertOk()
            ->assertJsonPath('message', 'User has been logged out from all devices.');

        expect($agent->tokens()->count())->toBe(0);
    });

    it('forbids non-admin from force logging out a user', function () {
        $agent = UserModel::factory()->create();
        $target = UserModel::factory()->create();

        Sanctum::actingAs($agent, ['*']);

        $this->deleteJson("/api/admin/users/{$target->id}/force-logout")
            ->assertForbidden();
    });

    it('requires authentication', function () {
        $user = UserModel::factory()->create();

        $this->deleteJson("/api/admin/users/{$user->id}/force-logout")
            ->assertUnauthorized();
    });
});

describe('toggle agent status', function () {
    it('allows admin to deactivate an agent', function () {
        $admin = UserModel::factory()->admin()->create();
        $agent = UserModel::factory()->create(['is_active' => true]);

        Sanctum::actingAs($admin, ['*']);

        $this->patchJson("/api/admin/users/{$agent->id}/toggle-status")
            ->assertOk()
            ->assertJsonPath('user.isActive', false);

        expect($agent->fresh()->is_active)->toBeFalse();
    });

    it('allows admin to re-activate an inactive agent', function () {
        $admin = UserModel::factory()->admin()->create();
        $agent = UserModel::factory()->inactive()->create();

        Sanctum::actingAs($admin, ['*']);

        $this->patchJson("/api/admin/users/{$agent->id}/toggle-status")
            ->assertOk()
            ->assertJsonPath('user.isActive', true);

        expect($agent->fresh()->is_active)->toBeTrue();
    });

    it('forbids toggling status of an admin user', function () {
        $admin = UserModel::factory()->admin()->create();
        $anotherAdmin = UserModel::factory()->admin()->create();

        Sanctum::actingAs($admin, ['*']);

        $this->patchJson("/api/admin/users/{$anotherAdmin->id}/toggle-status")
            ->assertForbidden();
    });

    it('forbids non-admin from toggling agent status', function () {
        $agent = UserModel::factory()->create();
        $target = UserModel::factory()->create();

        Sanctum::actingAs($agent, ['*']);

        $this->patchJson("/api/admin/users/{$target->id}/toggle-status")
            ->assertForbidden();
    });
});
