<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <title>Game Collection</title>
</head>
<body>
    <div class="container" style="margin:40px;">
        @auth
            @role('admin')
                <nav class="nav nav-pills mb-4" aria-label="Beheeromgeving">
                    <a class="nav-link" href="{{ route('admin.permissions.index') }}">Permissies</a>
                    <a class="nav-link" href="{{ route('admin.roles.index') }}">Rollen</a>
                    <a class="nav-link" href="{{ route('admin.role-permissions.index') }}">Permissies per rol</a>
                    <a class="nav-link" href="{{ route('admin.user-roles.index') }}">Rollen per gebruiker</a>
                </nav>
            @endrole
        @endauth

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="display-4">@yield('title')</h1>
        @yield('content')
    </div>
</body>
</html>