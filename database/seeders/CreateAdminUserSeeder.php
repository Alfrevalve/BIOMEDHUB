<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::firstOrCreate(
            ['email' => 'jesus.valera@biomedsac.com'],
            ['name' => 'Jesus Valera', 'password' => '123456789']
        );

        $user->syncRoles([$adminRole->name]);
    }
}
