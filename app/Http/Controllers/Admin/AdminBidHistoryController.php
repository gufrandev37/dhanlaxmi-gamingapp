<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamePlay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBidHistoryController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::orderBy('id', 'desc')->get();

        $gameId = $request->game_id ?? $games->first()?->id;

        $selectedGame = $games->firstWhere('id', $gameId);

        /*
        |--------------------------------------------------------------------------
        | JODI 00–99
        |--------------------------------------------------------------------------
        */

    $jodiRaw = GamePlay::where('game_id', $gameId)
    ->where('is_price_config', 0)
    ->selectRaw('number, SUM(amount) as total_amount')
    ->groupBy('number')
    ->pluck('total_amount', 'number')
    ->toArray();

$jodiData = [];

for ($i = 0; $i <= 99; $i++) {

    $intNumber = $i;              // 5
    $displayNumber = str_pad($i, 2, '0', STR_PAD_LEFT); // 05

    $jodiData[$displayNumber] = $jodiRaw[$intNumber] ?? 0;
}



        /*
        |--------------------------------------------------------------------------
        | ANDAR
        |--------------------------------------------------------------------------
        */
$andarRaw = DB::table('andar_plays')
    ->where('game_id', $gameId)
    ->selectRaw('number, SUM(amount) as total_amount')
    ->groupBy('number')
    ->pluck('total_amount', 'number')
    ->toArray();

$andarData = [];

for ($i = 0; $i <= 9; $i++) {
    $andarData[$i] = $andarRaw[$i] ?? 0;
}

  

        /*
        |--------------------------------------------------------------------------
        | BAHAR
        |--------------------------------------------------------------------------
        */

        $baharRaw = DB::table('bahar_plays')
            ->when($gameId, fn($q) => $q->where('game_id', $gameId))
            ->selectRaw('number, SUM(amount) as total_amount')
            ->groupBy('number')
            ->pluck('total_amount', 'number')
            ->toArray();

        $baharData = [];

        for ($i = 0; $i <= 9; $i++) {
            $baharData[$i] =
                $baharRaw[$i] ??
                $baharRaw[(string)$i] ??
                0;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $totalJodiAmount  = array_sum($jodiData);
        $totalAndarAmount = array_sum($andarData);
        $totalBaharAmount = array_sum($baharData);

        $totalBidAmount = $totalJodiAmount + $totalAndarAmount + $totalBaharAmount;

        $totalWinAmount = GamePlay::where('status', 'win')
            ->where('is_price_config', 0)
            ->when($gameId, fn($q) => $q->where('game_id', $gameId))
            ->sum('win_amount');

       $profit = $totalBidAmount - $totalWinAmount;

        return view('admin.bid-history', compact(
            'games',
            'gameId',
            'selectedGame',
            'jodiData',
            'andarData',
            'baharData',
            'totalJodiAmount',
            'totalAndarAmount',
            'totalBaharAmount',
            'totalBidAmount',
            'totalWinAmount',
            'profit'
        ));
    }
}