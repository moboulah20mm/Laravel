<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_management_pages_are_restricted_to_admins(): void
    {
        $urls = [
            route('admin.permissions.index'),
            route('admin.roles.index'),
            route('admin.role-permissions.index'),
            route('admin.user-roles.index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $customer = User::factory()->create();
        $customer->assignRole(Role::create(['name' => 'klant', 'guard_name' => 'web']));

        foreach ($urls as $url) {
            $this->actingAs($customer)->get($url)->assertForbidden();
        }
    }

    public function test_admin_can_manage_permissions_roles_and_both_assignments(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $this->actingAs($admin);

        $this->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertSee('Rollen per gebruiker');
        $this->get(route('admin.roles.index'))->assertOk();
        $this->get(route('admin.role-permissions.index'))->assertOk();
        $this->get(route('admin.user-roles.index'))->assertOk();

        $this->post(route('admin.permissions.store'), ['name' => 'eerste permissie'])
            ->assertRedirect(route('admin.permissions.index'));
        $firstPermission = Permission::where('name', 'eerste permissie')->firstOrFail();
        $this->assertSame('web', $firstPermission->guard_name);

        $this->post(route('admin.permissions.store'), ['name' => 'tweede permissie']);
        $secondPermission = Permission::where('name', 'tweede permissie')->firstOrFail();
        $this->get(route('admin.permissions.edit', $firstPermission))->assertOk();
        $this->put(route('admin.permissions.update', $firstPermission), ['name' => 'bijgewerkte permissie'])
            ->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['id' => $firstPermission->id, 'name' => 'bijgewerkte permissie', 'guard_name' => 'web']);

        $this->post(route('admin.roles.store'), ['name' => 'eerste rol'])
            ->assertRedirect(route('admin.roles.index'));
        $firstRole = Role::where('name', 'eerste rol')->firstOrFail();
        $this->assertSame('web', $firstRole->guard_name);

        $this->post(route('admin.roles.store'), ['name' => 'tweede rol']);
        $secondRole = Role::where('name', 'tweede rol')->firstOrFail();
        $this->get(route('admin.roles.edit', $firstRole))->assertOk();
        $this->put(route('admin.roles.update', $firstRole), ['name' => 'bijgewerkte rol'])
            ->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['id' => $firstRole->id, 'name' => 'bijgewerkte rol', 'guard_name' => 'web']);

        $this->post(route('admin.role-permissions.store'), [
            'permission_id' => $firstPermission->id,
            'role_id' => $firstRole->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->get(route('admin.role-permissions.edit', [$firstPermission->id, $firstRole->id]))->assertOk();
        $this->put(route('admin.role-permissions.update', [$firstPermission->id, $firstRole->id]), [
            'permission_id' => $secondPermission->id,
            'role_id' => $secondRole->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->assertDatabaseMissing('role_has_permissions', ['permission_id' => $firstPermission->id, 'role_id' => $firstRole->id]);
        $this->assertDatabaseHas('role_has_permissions', ['permission_id' => $secondPermission->id, 'role_id' => $secondRole->id]);
        $this->delete(route('admin.role-permissions.destroy', [$secondPermission->id, $secondRole->id]))
            ->assertRedirect(route('admin.role-permissions.index'));
        $this->assertDatabaseMissing('role_has_permissions', ['permission_id' => $secondPermission->id, 'role_id' => $secondRole->id]);

        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $this->post(route('admin.user-roles.store'), ['user_id' => $firstUser->id, 'role_id' => $firstRole->id])
            ->assertRedirect(route('admin.user-roles.index'));
        $this->get(route('admin.user-roles.edit', [$firstUser->id, $firstRole->id]))->assertOk();
        $this->put(route('admin.user-roles.update', [$firstUser->id, $firstRole->id]), [
            'user_id' => $secondUser->id,
            'role_id' => $secondRole->id,
        ])->assertRedirect(route('admin.user-roles.index'));
        $this->assertDatabaseMissing('model_has_roles', ['model_type' => User::class, 'model_id' => $firstUser->id, 'role_id' => $firstRole->id]);
        $this->assertDatabaseHas('model_has_roles', ['model_type' => User::class, 'model_id' => $secondUser->id, 'role_id' => $secondRole->id]);
        $this->delete(route('admin.user-roles.destroy', [$secondUser->id, $secondRole->id]))
            ->assertRedirect(route('admin.user-roles.index'));
        $this->assertDatabaseMissing('model_has_roles', ['model_type' => User::class, 'model_id' => $secondUser->id, 'role_id' => $secondRole->id]);

        $this->delete(route('admin.permissions.destroy', $firstPermission))->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseMissing('permissions', ['id' => $firstPermission->id]);
        $this->delete(route('admin.roles.destroy', $firstRole))->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $firstRole->id]);
    }

    public function test_duplicate_assignment_is_rejected_with_validation_feedback(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $permission = Permission::create(['name' => 'bekijken', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'redacteur', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->actingAs($admin)
            ->from(route('admin.role-permissions.create'))
            ->post(route('admin.role-permissions.store'), [
                'permission_id' => $permission->id,
                'role_id' => $role->id,
            ])
            ->assertRedirect(route('admin.role-permissions.create'))
            ->assertSessionHasErrors('permission_id');
    }
}
