@extends('base')

@section('title', $editing ? 'Koppeling wijzigen' : 'Permissie aan rol koppelen')

@section('content')
    <form method="post" action="{{ $editing ? route('admin.role-permissions.update', [$permissionId, $roleId]) : route('admin.role-permissions.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="permission_id">Permissie</label>
            <select id="permission_id" name="permission_id" class="form-control" required>
                <option value="">Selecteer een permissie</option>
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}" @selected((string) old('permission_id', $permissionId) === (string) $permission->id)>{{ $permission->name }} (ID: {{ $permission->id }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="role_id">Rol</label>
            <select id="role_id" name="role_id" class="form-control" required>
                <option value="">Selecteer een rol</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id', $roleId) === (string) $role->id)>{{ $role->name }} (ID: {{ $role->id }})</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Opslaan</button>
        <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
