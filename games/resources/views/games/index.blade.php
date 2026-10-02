@extends('base')

@section('title', '🎮 Game Collection')

@section('content')
    <a href="/games/create" class="btn btn-success mb-3">Add Game</a>

    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Game</th>
                <th>Platform</th>
                <th>Rating</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @php( $sum = 0 )
            @foreach($games as $game)
                @php( $sum += $game->rating )
                <tr>
                    <td>{{ $game->id }}</td>
                    <td>{{ $game->game_name }}</td>
                    <td>{{ $game->platform }}</td>
                    <td>{{ $game->rating }}/10</td>
                    <td>
                        <a href="/games/edit/{{ $game->id }}" class="btn btn-primary btn-sm">Edit</a>
                    </td>
                    <td>
                        <form action="/games/destroy/{{ $game->id }}" method="post">
                            @csrf
                            <button onclick="return confirm('Weet je het zeker?')" class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><strong>Gemiddelde rating:</strong></td>
                <td><strong>{{ count($games) > 0 ? number_format($sum / count($games), 1) : 0 }}/10</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endsection