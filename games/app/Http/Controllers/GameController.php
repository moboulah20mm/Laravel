<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();
        return view('games.index', compact('games'));
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_name' => 'required',
            'platform' => 'required',
            'genre' => 'required',
            'rating' => 'required|numeric|min:0|max:10'
        ]);

        $game = new Game([
            'game_name' => $request->get('game_name'),
            'platform' => $request->get('platform'),
            'genre' => $request->get('genre'),
            'rating' => $request->get('rating')
        ]);

        $game->save();

        return redirect('/games')->with('success', 'Game added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $game = Game::findOrFail($id);

        return view('games.show', ['game' => $game]);
    }

    /**
     * Show the form for editing the specified resource.
     * ✔ Toegevoegd: edit functie
     */
    public function edit($id)
    {
        $game = Game::find($id);
        return view('games.edit', ['game' => $game]);
    }

    /**
     * Update the specified resource in storage.
     * ✔ Toegevoegd: update functie
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'game_name' => 'required',
            'platform' => 'required',
            'genre' => 'required',
            'rating' => 'required|numeric|min:0|max:10'
        ]);

        $game = Game::find($id);
        $game->game_name = $request->get('game_name');
        $game->platform = $request->get('platform');
        $game->genre = $request->get('genre');
        $game->rating = $request->get('rating');
        $game->save();

        return redirect('/games');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $game = Game::find($id);
        $game->delete();

        return redirect('/games');
    }
}