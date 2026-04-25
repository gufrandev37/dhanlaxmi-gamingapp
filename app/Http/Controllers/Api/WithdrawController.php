<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Models\Wallet;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:100',
            'payment_mode' => 'required|in:upi,bank,google_pay,paytm,phonepe',
            'mobile'       => 'required|string|max:15',
        ]);

        $user = $request->user();

        // Check balance
        $userWallet = UserWallet::where('user_id', $user->id)->first();
        $balance    = $userWallet ? $userWallet->balance : 0;

        if ($request->amount > $balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // ✅ Just create withdrawal REQUEST — do NOT debit wallet yet
            $withdraw = Withdraw::create([
                'user_id'      => $user->id,
                'payment_mode' => $request->payment_mode,
                'mobile'       => $request->mobile,
                'amount'       => $request->amount,
                'status'       => 'processing', // pending admin approval
            ]);

            // Generate CIN
            $withdraw->cin = 'WD-' . str_pad($withdraw->id, 6, '0', STR_PAD_LEFT);
            $withdraw->save();

            // ✅ NO wallet debit here — only debit when admin approves

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted. Waiting for admin approval.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $withdraws = Withdraw::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get(['id', 'amount', 'payment_mode', 'mobile', 'status', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $withdraws,
        ]);
    }
}