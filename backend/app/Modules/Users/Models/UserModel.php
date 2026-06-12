<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\Enums\UserRoleEnum;
use Database\Factories\UserModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property UserRoleEnum $role
 */
#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class UserModel extends Authenticatable
{
    protected $table = 'users';

    /** @use HasFactory<UserModelFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory(): UserModelFactory
    {
        return UserModelFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user role is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRoleEnum::Admin;
    }

    /**
     * Check if user role is agent
     */
    public function isAgent(): bool
    {
        return $this->role === UserRoleEnum::Agent;
    }
}
