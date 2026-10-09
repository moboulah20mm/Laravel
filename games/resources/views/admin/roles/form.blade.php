@extends('base')

@section('title', $role->exists ? 'Rol wijzigen' : 'Rol toevoegen')

@section('content')
    <form method="post" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
        @csrf
        @if ($role->exists)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" name="name" class="form-control" value="{{ old('name', $role->name) }}" required maxlength="255">
        </div>

        <button class="btn btn-primary" type="submit">Opslaan</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
