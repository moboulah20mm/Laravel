@extends('base')

@section('title', 'Permissies beheren')

@section('content')
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-success mb-3">Permissie toevoegen</a>

    <table class="table">
        <thead>
            <tr><th>ID</th><th>Naam</th><th>Acties</th></tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>{{ $permission->id }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary btn-sm">Wijzigen</a>
                        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Er zijn nog geen permissies.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
