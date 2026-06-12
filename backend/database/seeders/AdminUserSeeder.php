<?php

namespace Database\Seeders;

use App\Modules\Users\Enums\UserRoleEnum;
use App\Modules\Users\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Admin');

        if (! $email || ! $password) {
            $this->command->warn('Skipping admin seeder: ADMIN_EMAIL or ADMIN_PASSWORD is not set in .env');

            return;
        }

        UserModel::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => UserRoleEnum::Admin,
                'is_active' => true,
            ]
        );

        $this->command->info("Admin user seeded: {$email}");
    }
}
