@extends('base')

@section('title', 'Permissies aan rollen koppelen')

@section('content')
    <a href="{{ route('admin.role-permissions.create') }}" class="btn btn-success mb-3">Koppeling toevoegen</a>

    <table class="table">
        <thead>
            <tr><th>Permissie-ID</th><th>Permissie</th><th>Rol-ID</th><th>Rol</th><th>Acties</th></tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->permission_id }}</td>
                    <td>{{ $assignment->permission_name }}</td>
                    <td>{{ $assignment->role_id }}</td>
                    <td>{{ $assignment->role_name }}</td>
                    <td>
                        <a href="{{ route('admin.role-permissions.edit', [$assignment->permission_id, $assignment->role_id]) }}" class="btn btn-primary btn-sm">Wijzigen</a>
                        <form action="{{ route('admin.role-permissions.destroy', [$assignment->permission_id, $assignment->role_id]) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Er zijn nog geen permissies aan rollen gekoppeld.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
