@extends('base')

@section('title', $permission->exists ? 'Permissie wijzigen' : 'Permissie toevoegen')

@section('content')
    <form method="post" action="{{ $permission->exists ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
        @csrf
        @if ($permission->exists)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name">Naam</label>
            <input id="name" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required maxlength="255">
        </div>

        <button class="btn btn-primary" type="submit">Opslaan</button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
@endsection
