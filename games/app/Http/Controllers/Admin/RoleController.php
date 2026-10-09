<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => new Role]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')],
        ]);

        Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        return redirect()->route('admin.roles.index')->with('status', 'Rol aangemaakt.');
    }

    public function edit(Role $role): View
    {
        abort_unless($role->guard_name === 'web', 404);

        return view('admin.roles.form', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')->ignore($role->id)],
        ]);

        $role->update(['name' => $validated['name']]);

        return redirect()->route('admin.roles.index')->with('status', 'Rol bijgewerkt.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Rol verwijderd.');
    }
}
