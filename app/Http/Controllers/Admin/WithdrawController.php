<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Models\Wallet;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdraw::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cin', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $withdraws = $query->latest()->paginate(10);

        // Stats
        $totalPending  = Withdraw::where('status', 'processing')->count();
        $totalApproved = Withdraw::where('status', 'approved')->count();
        $totalRejected = Withdraw::where('status', 'rejected')->count();

        return view('admin.payment-withdraw', compact(
            'withdraws', 'totalPending', 'totalApproved', 'totalRejected'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $withdraw = Withdraw::with('user')->findOrFail($id);

        if ($withdraw->status !== 'processing') {
            return back()->with('error', 'Only processing requests can be updated.');
        }

        DB::beginTransaction();

        try {
            if ($request->status === 'approved') {

                // ✅ Check user still has enough balance
                $userWallet = UserWallet::where('user_id', $withdraw->user_id)->first();
                $balance    = $userWallet ? $userWallet->balance : 0;

                if ($balance < $withdraw->amount) {
                    return back()->with('error', 'User has insufficient balance.');
                }

                // ✅ Deduct wallet ONLY on approval
                $userWallet->decrement('balance', $withdraw->amount);

                // Store wallet history
                Wallet::create([
                    'user_id' => $withdraw->user_id,
                    'cin'     => $withdraw->cin,
                    'amount'  => $withdraw->amount,
                    'type'    => 'debit',
                ]);
            }

            // Update withdrawal status
            $withdraw->update(['status' => $request->status]);

            DB::commit();

            $message = $request->status === 'approved'
                ? 'Withdrawal approved and amount deducted from user wallet.'
                : 'Withdrawal request rejected.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}