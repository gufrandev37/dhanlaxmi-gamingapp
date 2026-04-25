<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GamePlay;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\UserWallet;




class GamePlayController extends Controller
{
    //  ADD PLAY (Add Button)


    // public function add(Request $request, $gameId)
    // {
    //     $request->validate([
    //         'play_type' => 'required|in:jodi,crossing,copy_paste',
    //         'number' => 'required|string',
    //         'amount' => 'required|integer|min:1',
    //     ]);

    //     $user = $request->user();

    //     //  Check game
    //     $game = Game::find($gameId);
    //     if (!$game) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Game not found'
    //         ], 404);
    //     }

    //     if ($game->status !== 'play') {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Game closed'
    //         ], 400);
    //     }

    //     //  Calculate wallet balance
    //     $credit = Wallet::where('user_id', $user->id)
    //         ->where('type', 'credit')
    //         ->sum('amount');

    //     $debit = Wallet::where('user_id', $user->id)
    //         ->where('type', 'debit')
    //         ->sum('amount');

    //     $balance = $credit - $debit;

    //     //  Insufficient balance
    //     if ($balance < $request->amount) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Insufficient wallet balance'
    //         ], 400);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         //  Deduct wallet (DEBIT)
    //         Wallet::create([
    //             'user_id' => $user->id,
    //             'cin' => $user->cin ?? null,
    //             'amount' => $request->amount,
    //             'type' => 'debit',
    //         ]);

    //         //  Create play
    //         $play = GamePlay::create([
    //             'user_id' => $user->id,
    //             'game_id' => $gameId,
    //             'play_type' => $request->play_type,
    //             'number' => $request->number,
    //             'amount' => $request->amount,
    //             'status' => 'pending',
    //         ]);

    //         //  Win logic
    //         if ($game->correct_answer && $request->number == $game->correct_answer) {
    //             $play->update(['status' => 'win']);

    //             // Credit winning (example 2x)
    //             Wallet::create([
    //                 'user_id' => $user->id,
    //                 'cin' => $user->cin ?? null,
    //                 'amount' => $request->amount * 2,
    //                 'type' => 'credit',
    //             ]);
    //         }

    //         DB::commit();

    //         //  Updated balance
    //         $newBalance =
    //             Wallet::where('user_id', $user->id)->where('type', 'credit')->sum('amount')
    //             - Wallet::where('user_id', $user->id)->where('type', 'debit')->sum('amount');

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Game played successfully',
    //             'play' => $play,
    //             'balance' => $newBalance
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Game play failed',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function add(Request $request, $gameId)
{
    $request->validate([
        'play_type' => 'required|in:jodi,crossing,copy_paste',
        'number'    => 'required|string|size:2',
        'amount'    => 'required|integer|min:1',
        'palti'     => 'nullable|boolean'
    ]);

    $user = $request->user();

    $game = Game::findOrFail($gameId);

    if ($game->status !== 'play') {
        return response()->json([
            'status' => false,
            'message' => 'Game closed'
        ], 400);
    }

    // ======================================
    // NUMBER LOGIC
    // ======================================

    $numbersToPlay = [];
    $inputNumber = $request->number;

    if ($request->play_type === 'jodi') {
        $numbersToPlay[] = $inputNumber;
    }

    elseif ($request->play_type === 'crossing') {

        $a = $inputNumber[0];
        $b = $inputNumber[1];

        $numbersToPlay = [
            $a.$b,
            $b.$a,
            $a.$a,
            $b.$b,
        ];
    }

    elseif ($request->play_type === 'copy_paste') {

        $withPalti = $request->palti ?? false;

        if ($withPalti) {
            $numbersToPlay = [
                $inputNumber,
                strrev($inputNumber)
            ];
        } else {
            $numbersToPlay[] = $inputNumber;
        }
    }

    $numbersToPlay = array_unique($numbersToPlay);

    $totalAmount = $request->amount * count($numbersToPlay);

    DB::beginTransaction();

    try {

        // ======================================
        // LOCK USER WALLET
        // ======================================

        $userWallet = UserWallet::where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$userWallet) {
            $userWallet = UserWallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
        }

        // CHECK BALANCE
        if ($userWallet->balance < $totalAmount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // DEDUCT BALANCE
        $userWallet->decrement('balance', $totalAmount);

        // STORE WALLET HISTORY
        Wallet::create([
            'user_id' => $user->id,
            'cin'     => $user->cin ?? null,
            'amount'  => $totalAmount,
            'type'    => 'debit',
        ]);

        $plays = [];

        foreach ($numbersToPlay as $number) {

            $play = GamePlay::create([
                'user_id'   => $user->id,
                'game_id'   => $gameId,
                'play_type' => $request->play_type,
                'number'    => $number,
                'amount'    => $request->amount,
                'status'    => 'pending',
            ]);

            // WIN CHECK
            if ($game->correct_answer && $number == $game->correct_answer) {

                $play->update(['status' => 'win']);

                $winAmount = $request->amount * 2;

                // ADD WINNING BALANCE
                $userWallet->increment('balance', $winAmount);

                Wallet::create([
                    'user_id' => $user->id,
                    'cin'     => $user->cin ?? null,
                    'amount'  => $winAmount,
                    'type'    => 'credit',
                ]);
            }

            $plays[] = $play;
        }

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Game played successfully',
            'plays'   => $plays,
            'balance' => $userWallet->fresh()->balance
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Game play failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // SHOW USER PLAYS (table below Add)
    public function list($gameId)
    {
        $plays = GamePlay::where('user_id', auth()->id())
            ->where('game_id', $gameId)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $plays
        ]);
    }
}