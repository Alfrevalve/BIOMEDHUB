<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'logistica',
            'instrumentista',
            'comercial',
            'soporte_biomedico',
            'auditoria',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web']
            );
        }

        if ($admin = User::where('email', 'test@example.com')->first()) {
            $admin->syncRoles(['admin']);
        }
    }
}
