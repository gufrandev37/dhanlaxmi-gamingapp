<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GamePlay;

class WinningHistoryController extends Controller
{
       public function index()   // when display winning history when game status will be closed 
{
    $userId = auth()->id();

    // Fetch winning plays where the game is closed
    $wins = GamePlay::with('game')
        ->where('user_id', $userId)
        ->whereHas('game', function ($query) {
            $query->where('status', 'close'); // only closed games
        })
        ->where('status', 'win') // only plays marked as win
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($play) {
            return [
                'game_id'        => $play->game_id,
                'game_name'      => $play->game?->game_name ?? 'N/A',
                'play_type'      => $play->play_type,
                'number'         => $play->number,
                'amount'         => $play->amount,
                'correct_answer' => $play->game?->correct_answer ?? 'N/A',
                'played_date'    => $play->created_at->format('d-m-Y'),
                'status'         => $play->status,
            ];
        });

    return response()->json([
        'status' => true,
        'data'   => $wins
    ]);
}
}