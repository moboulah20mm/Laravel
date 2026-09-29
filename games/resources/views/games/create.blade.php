<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <title>Add Game</title>
</head>
<body>
    <div class="container" style="margin:40px;">
        <h1 class="display-4">🎮 Add Game</h1>
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
    </div>
</body>
</html>