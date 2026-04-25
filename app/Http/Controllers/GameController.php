<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Game List
    public function index()
    {
        $games = Game::latest()->paginate(10);
        return view('admin.games', compact('games'));
    }

    // Show Create Form
    public function create()
    {
        $users = User::all();
        return view('admin.game.create', compact('users'));
    }

    // Store Game
    public function store(Request $request)
    {
        $request->validate([
            'game_name' => 'required|string|max:255',
            'status' => 'required|in:play,close',
            'result_time' => 'required',
            'close_time' => 'required',
            'play_next_day' => 'required|in:yes,no',
            'play_days' => 'nullable|array',
        ]);

        Game::create([
            'game_name' => $request->game_name,
            'status' => $request->status,
            'result_time' => $request->result_time,
            'close_time' => $request->close_time,
            'play_next_day' => $request->play_next_day,
            'play_days' => $request->play_days,
        ]);

        return redirect()->route('admin.games')
            ->with('success', 'Game created successfully');
    }
    public function edit($id)
    {
        $game = Game::findOrFail($id);
        $users = User::all();

        return view('admin.games.edit', compact('game', 'users'));
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $request->validate([
            'game_name' => 'required|string|max:255',
            'status' => 'required|in:play,close',
            'result_time' => 'nullable',
            'close_time' => 'nullable',
            'play_next_day' => 'required|in:yes,no',
            'play_days' => 'nullable|array',
        ]);

        $game->update([
            'game_name' => $request->game_name,
            'status' => $request->status,
            'result_time' => $request->result_time,
            'close_time' => $request->close_time,
            'play_next_day' => $request->play_next_day,
            'play_days' => $request->play_days,
        ]);

        return redirect()->route('admin.games')
            ->with('success', 'Game updated successfully');
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return back()->with('success', 'Game deleted successfully');
    }
    public function toggleStatus($id)
    {
        $game = Game::findOrFail($id);

        $game->status = $game->status === 'play' ? 'close' : 'play';
        $game->save();

        return back()->with('success', 'Game status updated successfully');
    }
}
