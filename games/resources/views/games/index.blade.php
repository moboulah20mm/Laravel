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
        <h1 class="display-4">🎮 Game Collection</h1>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Game</th>
                    <th>Platform</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach($games as $game) {{-- in deze loop worden alle rijen (records) gemaakt die in de database zijn gevonden. --}}
                    <tr>
                        <td>{{ $game->id }}</td>
                        <td>{{ $game->game_name }}</td>
                        <td>{{ $game->platform }}</td>
                        <td>{{ $game->rating }}/10</td> 
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>