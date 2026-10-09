<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $assignments = DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
            ->where('permissions.guard_name', 'web')
            ->where('roles.guard_name', 'web')
            ->select('permissions.id as permission_id', 'permissions.name as permission_name', 'roles.id as role_id', 'roles.name as role_name')
            ->orderBy('roles.name')
            ->orderBy('permissions.name')
            ->get();

        return view('admin.role-permissions.index', compact('assignments'));
    }

    public function create(): View
    {
        return view('admin.role-permissions.form', [
            'permissions' => Permission::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'permissionId' => null,
            'roleId' => null,
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentIsUnique($validated['permission_id'], $validated['role_id']);

        Role::findOrFail($validated['role_id'])->givePermissionTo(
            Permission::findOrFail($validated['permission_id'])
        );

        return redirect()->route('admin.role-permissions.index')->with('status', 'Permissie aan rol gekoppeld.');
    }

    public function edit(string $permission, string $role): View
    {
        $this->findAssignment($permission, $role);

        return view('admin.role-permissions.form', [
            'permissions' => Permission::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'permissionId' => (int) $permission,
            'roleId' => (int) $role,
            'editing' => true,
        ]);
    }

    public function update(Request $request, string $permission, string $role): RedirectResponse
    {
        $oldPermission = Permission::where('guard_name', 'web')->findOrFail($permission);
        $oldRole = Role::where('guard_name', 'web')->findOrFail($role);
        abort_unless($oldRole->hasPermissionTo($oldPermission), 404);

        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentIsUnique($validated['permission_id'], $validated['role_id'], $permission, $role);

        DB::transaction(function () use ($oldPermission, $oldRole, $validated): void {
            $oldRole->revokePermissionTo($oldPermission);
            Role::findOrFail($validated['role_id'])->givePermissionTo(
                Permission::findOrFail($validated['permission_id'])
            );
        });

        return redirect()->route('admin.role-permissions.index')->with('status', 'Koppeling bijgewerkt.');
    }

    public function destroy(string $permission, string $role): RedirectResponse
    {
        $assignment = $this->findAssignment($permission, $role);
        $assignment['role']->revokePermissionTo($assignment['permission']);

        return redirect()->route('admin.role-permissions.index')->with('status', 'Koppeling verwijderd.');
    }

    private function validateAssignment(Request $request): array
    {
        return $request->validate([
            'permission_id' => ['required', 'integer', Rule::exists('permissions', 'id')->where('guard_name', 'web')],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
        ]);
    }

    private function ensureAssignmentIsUnique(int|string $permissionId, int|string $roleId, ?string $exceptPermission = null, ?string $exceptRole = null): void
    {
        if ((string) $permissionId === $exceptPermission && (string) $roleId === $exceptRole) {
            return;
        }

        if (DB::table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', $roleId)->exists()) {
            throw ValidationException::withMessages(['permission_id' => 'Deze permissie is al aan deze rol gekoppeld.']);
        }
    }

    private function findAssignment(string $permissionId, string $roleId): array
    {
        $permission = Permission::where('guard_name', 'web')->findOrFail($permissionId);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        abort_unless($role->hasPermissionTo($permission), 404);

        return compact('permission', 'role');
    }
}
