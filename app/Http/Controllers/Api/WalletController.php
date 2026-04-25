<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\GamePlay;



class WalletController extends Controller
{

    public function walletBalance($userId)
    {
        $credit = Wallet::where('user_id', $userId)
            ->where('type', 'credit')
            ->sum('amount');

        $debit = Wallet::where('user_id', $userId)
            ->where('type', 'debit')
            ->sum('amount');

        return $credit - $debit;
    }

    /**
     * ADD FUNDS (CREDIT)
     * POST /api/wallet/add-funds
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50|max:10000',
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            Wallet::create([
                'user_id' => $user->id,
                'cin' => $user->cin ?? null,
                'amount' => $request->amount,
                'type' => 'credit',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Funds added successfully',
                'balance' => $this->walletBalance($user->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to add funds',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function history(Request $request)
    {
        $user = $request->user();

        $history = GamePlay::with('game:id,city_name')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($play) {
                return [
                    'amount' => $play->amount,
                    'date' => $play->created_at->format('d-m-Y H:i'),

                    'game' => [
                        'game_id' => $play->game_id,
                        'game_name' => $play->game->city_name ?? null,
                        'play_type' => $play->play_type,
                        'number' => $play->number,
                        'status' => $play->status,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

}