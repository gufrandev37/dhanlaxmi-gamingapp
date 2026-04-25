<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;


class GameController extends Controller
{
    // GAME LIST (First Screen)
    public function index()
    {
        $games = Game::select(
            'id',
            'game_name',
            'status',
            'result_time',
            'close_time',
            'play_next_day',
            'play_days'
        )
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $games
        ]);
    }

    // SINGLE GAME (When user clicks Delhi Bazar)
    public function show($id)
    {
        $game = Game::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $game->id,
                'game_name' => $game->game_name,
                'status' => $game->status,
                'result_time' => $game->result_time,
                'close_time' => $game->close_time,
                'play_next_day' => $game->play_next_day,
                'play_days' => $game->play_days,
            ]
        ]);
    }


    public function correctAnswers()
    {
        // Fetch all closed games
        $games = Game::where('status', 'close')
            ->select('city_name', 'correct_answer', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by date (Y-m-d)
        $grouped = $games->groupBy(function ($game) {
            return $game->created_at->format('Y-m-d'); // group by date
        });

        // Format for JSON
        $result = [];
        foreach ($grouped as $date => $gamesOnDate) {
            $result[] = [
                'date' => $date,
                'games' => $gamesOnDate->map(function ($game) {
                    return [
                        'city_name' => $game->city_name,
                        'correct_answer' => $game->correct_answer,
                    ];
                })->values()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }


    public function chartFilter(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        // Fetch only required records
        $games = Game::where('status', 'close')
            ->whereMonth('created_at', $request->month)
            ->whereYear('created_at', $request->year)
            ->select('city_name', 'correct_answer', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by date
        $grouped = $games->groupBy(function ($game) {
            return $game->created_at->format('Y-m-d');
        });

        $result = [];

        foreach ($grouped as $date => $gamesOnDate) {

            $result[] = [
                'date' => $date,
                'games' => $gamesOnDate->map(function ($game) {
                    return [
                        'city_name' => $game->city_name,
                        'correct_answer' => $game->correct_answer ?? 'xx',
                    ];
                })->values()
            ];
        }

        return response()->json([
            'success' => true,
            'month' => $request->month,
            'year' => $request->year,
            'data' => $result
        ]);
    }

}

