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

        // Asegurar usuario admin principal
        $adminEmail = env('ADMIN_EMAIL', 'jesus.valera@biomedsac.com.pe');
        $adminPassword = env('ADMIN_PASSWORD', 'CambiaEstaClave123!');

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            ['name' => 'Administrador', 'password' => $adminPassword]
        );

        $admin->syncRoles(['admin']);
    }
}
