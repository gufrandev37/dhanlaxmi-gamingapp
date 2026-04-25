<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();

        // Calculate wallet balance (sum of credits - debits)
        $walletAmount = $user->wallets()
            ->selectRaw('SUM(CASE WHEN type="credit" THEN amount ELSE -amount END) as balance')
            ->value('balance');

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'customer_id' => $user->cin,
                'password' => $user->password, // hashed password
                'wallet_amount' => $walletAmount ?? 0,
            ],
        ], 200);
    }
}