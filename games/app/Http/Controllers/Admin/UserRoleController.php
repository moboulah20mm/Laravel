<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(): View
    {
        $assignments = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('roles.guard_name', 'web')
            ->select('users.id as user_id', 'users.name as user_name', 'users.email as user_email', 'roles.id as role_id', 'roles.name as role_name')
            ->orderBy('users.name')
            ->orderBy('roles.name')
            ->get();

        return view('admin.user-roles.index', compact('assignments'));
    }

    public function create(): View
    {
        return view('admin.user-roles.form', [
            'users' => User::query()->orderBy('name')->get(),
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'userId' => null,
            'roleId' => null,
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentIsUnique($validated['user_id'], $validated['role_id']);

        User::findOrFail($validated['user_id'])->assignRole(Role::findOrFail($validated['role_id']));

        return redirect()->route('admin.user-roles.index')->with('status', 'Rol aan gebruiker gekoppeld.');
    }

    public function edit(string $user, string $role): View
    {
        $this->findAssignment($user, $role);

        return view('admin.user-roles.form', [
            'users' => User::query()->orderBy('name')->get(),
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
            'userId' => (int) $user,
            'roleId' => (int) $role,
            'editing' => true,
        ]);
    }

    public function update(Request $request, string $user, string $role): RedirectResponse
    {
        $oldUser = User::findOrFail($user);
        $oldRole = Role::where('guard_name', 'web')->findOrFail($role);
        abort_unless($oldUser->hasRole($oldRole), 404);

        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentIsUnique($validated['user_id'], $validated['role_id'], $user, $role);

        DB::transaction(function () use ($oldUser, $oldRole, $validated): void {
            $oldUser->removeRole($oldRole);
            User::findOrFail($validated['user_id'])->assignRole(Role::findOrFail($validated['role_id']));
        });

        return redirect()->route('admin.user-roles.index')->with('status', 'Koppeling bijgewerkt.');
    }

    public function destroy(string $user, string $role): RedirectResponse
    {
        $assignment = $this->findAssignment($user, $role);
        $assignment['user']->removeRole($assignment['role']);

        return redirect()->route('admin.user-roles.index')->with('status', 'Koppeling verwijderd.');
    }

    private function validateAssignment(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
        ]);
    }

    private function ensureAssignmentIsUnique(int|string $userId, int|string $roleId, ?string $exceptUser = null, ?string $exceptRole = null): void
    {
        if ((string) $userId === $exceptUser && (string) $roleId === $exceptRole) {
            return;
        }

        if (DB::table('model_has_roles')->where('model_type', User::class)->where('model_id', $userId)->where('role_id', $roleId)->exists()) {
            throw ValidationException::withMessages(['role_id' => 'Deze rol is al aan deze gebruiker gekoppeld.']);
        }
    }

    private function findAssignment(string $userId, string $roleId): array
    {
        $user = User::findOrFail($userId);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        abort_unless($user->hasRole($role), 404);

        return compact('user', 'role');
    }
}
