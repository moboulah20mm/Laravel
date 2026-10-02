@extends('base')

@section('title', '🎮 Add Game')

@section('content')
    <form method="post" action="/games/store">
        @csrf
        <div class="form-group">
            <label>Game Name *</label>
            <input type="text" class="form-control" name="game_name" />
        </div>
        <div class="form-group">
            <label>Platform *</label>
            <input type="text" class="form-control" name="platform" placeholder="PS5, Xbox, PC, Switch..." />
        </div>
        <div class="form-group">
            <label>Genre *</label>
            <input type="text" class="form-control" name="genre" placeholder="Action, RPG, Sports..." />
        </div>
        <div class="form-group">
            <label>Rating (0-10)</label>
            <input type="number" step="0.1" min="0" max="10" class="form-control" name="rating" />
        </div>
        <button type="submit" class="btn btn-success">Add Game</button>
    </form>
@endsection