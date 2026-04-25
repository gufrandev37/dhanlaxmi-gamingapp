<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AndarPlay;
use App\Models\BaharPlay;
use App\Models\Wallet;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use App\Models\UserWallet;






class AndarBaharController extends Controller
{
public function playAndar(Request $request, $gameId)
{
    $request->validate([
        'number' => 'required|integer|between:0,9',
        'amount' => 'required|integer|min:1',
    ]);

    $user = $request->user();

    DB::beginTransaction();

    try {

        // 🔐 Lock wallet
        $wallet = UserWallet::where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            $wallet = UserWallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
        }

        if ($wallet->balance < $request->amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // 💰 Deduct balance
        $wallet->decrement('balance', $request->amount);

        // 📜 Store history
        Wallet::create([
            'user_id' => $user->id,
            'cin'     => $user->cin ?? null,
            'amount'  => $request->amount,
            'type'    => 'debit',
        ]);

        // 🎯 Create Andar play
        $play = AndarPlay::create([
            'user_id' => $user->id,
            'game_id' => $gameId,
            'number'  => $request->number,
            'amount'  => $request->amount,
            'status'  => 'pending',
        ]);

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Andar play placed successfully',
            'data'    => $play,
            'balance' => $wallet->fresh()->balance
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // 🔥 SAME LOGIC FOR BAHAR
    public function playBahar(Request $request, $gameId)
{
    $request->validate([
        'number' => 'required|integer|between:0,9',
        'amount' => 'required|integer|min:1',
    ]);

    $user = $request->user();

    DB::beginTransaction();

    try {

        $wallet = UserWallet::where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            $wallet = UserWallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);
        }

        if ($wallet->balance < $request->amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        $wallet->decrement('balance', $request->amount);

        Wallet::create([
            'user_id' => $user->id,
            'cin'     => $user->cin ?? null,
            'amount'  => $request->amount,
            'type'    => 'debit',
        ]);

        $play = BaharPlay::create([
            'user_id' => $user->id,
            'game_id' => $gameId,
            'number'  => $request->number,
            'amount'  => $request->amount,
            'status'  => 'pending',
        ]);

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Bahar play placed successfully',
            'data'    => $play,
            'balance' => $wallet->fresh()->balance
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}
}