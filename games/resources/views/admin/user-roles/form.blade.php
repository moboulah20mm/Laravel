@extends('base')

@section('title', $editing ? 'Koppeling wijzigen' : 'Rol aan gebruiker koppelen')

@section('content')
    <form method="post" action="{{ $editing ? route('admin.user-roles.update', [$userId, $roleId]) : route('admin.user-roles.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="user_id">Gebruiker</label>
            <select id="user_id" name="user_id" class="form-control" required>
                <option value="">Selecteer een gebruiker</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $userId) === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
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
        <a href="{{ route('admin.user-roles.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
