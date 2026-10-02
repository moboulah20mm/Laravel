@extends('base')

@section('title', '🎮 Game Details')

@section('content')
    <table class="table">
        <tbody>
            <tr>
                <th>Game name</th>
                <td>{{ $game->game_name }}</td>
            </tr>
            <tr>
                <th>Platform</th>
                <td>{{ $game->platform }}</td>
            </tr>
            <tr>
                <th>Genre</th>
                <td>{{ $game->genre }}</td>
            </tr>
            <tr>
                <th>Rating</th>
                <td>{{ $game->rating }}/10</td>
            </tr>
        </tbody>
    </table>

    <a href="/games" class="btn btn-secondary">Back to games</a>
@endsection