@extends('base')

@section('title', 'Rollen beheren')

@section('content')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-success mb-3">Rol toevoegen</a>

    <table class="table">
        <thead>
            <tr><th>ID</th><th>Naam</th><th>Acties</th></tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">Wijzigen</a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Er zijn nog geen rollen.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
