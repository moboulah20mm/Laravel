<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $permissionNames = [
            'product bekijken',
            'product bestellen',
            'product invoeren',
            'product aanpassen',
            'product verwijderen',
            'bestellingen bekijken',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $customerRole = Role::findOrCreate('klant', 'web');
        $adminRole = Role::findOrCreate('admin', 'web');

        $customerRole->syncPermissions([
            'product bekijken',
            'product bestellen',
        ]);
        $adminRole->syncPermissions($permissionNames);

        $customerUser = User::query()->where('email', 'klant@klant.nl')->firstOrFail();
        $adminUser = User::query()->where('email', 'admin@admin.nl')->firstOrFail();

        $customerUser->syncRoles([$customerRole]);
        $adminUser->syncRoles([$adminRole]);
    }
}
