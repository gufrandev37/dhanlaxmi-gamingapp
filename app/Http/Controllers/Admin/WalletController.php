<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Wallet;
use App\Models\UserWallet;


class WalletController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'search' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        // ✅ Base Query with eager loading
        $query = Wallet::with('user');

        // 🔍 Search Filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('cin', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // 📅 Date Filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // 📄 Pagination
        $wallets = $query->latest()->paginate(10);

        // 📊 KPI Cards
        $totalUsers = User::count();
        $activeUsers = User::where('status', 1)->count();
        $inactiveUsers = User::where('status', 0)->count();

        return view('admin.wallet-history', compact(
            'wallets',
            'totalUsers',
            'activeUsers',
            'inactiveUsers'
        ));
    }


    public function walletDashboard()
    {
        return view('admin.wallet');
    }

    // Admin Credit / Debit
    public function updateWallet(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'mobile' => 'required|digits_between:10,15',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {

            $user = User::where('phone', $request->mobile)->first();

            if (!$user) {
                return back()
                    ->withInput()
                    ->with('error', 'No user found with mobile number: ' . $request->mobile);
            }

            //  Lock wallet row to prevent race condition
            $userWallet = \App\Models\UserWallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$userWallet) {
                $userWallet = \App\Models\UserWallet::create([
                    'user_id' => $user->id,
                    'balance' => 0
                ]);
            }

            // Check sufficient balance for debit
            if ($request->type === 'debit' && $request->amount > $userWallet->balance) {
                return back()
                    ->withInput()
                    ->with('error', "Insufficient balance. Current balance: ₹" . number_format($userWallet->balance, 2));
            }

            // ✅ Update balance FIRST
            if ($request->type === 'credit') {
                $userWallet->balance += $request->amount;
            } else {
                $userWallet->balance -= $request->amount;
            }

            $userWallet->save();

            // ✅ Insert transaction log
            Wallet::create([
                'user_id' => $user->id,
                'cin' => $user->cin,
                'amount' => $request->amount,
                'type' => $request->type,
            ]);

            DB::commit();

            return back()->with(
                'success',
                "✅ " . ucfirst($request->type) . " of ₹" . number_format($request->amount, 2) .
                " done for <strong>" . $user->name . "</strong>. New Balance: ₹" .
                number_format($userWallet->balance, 2)
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // AJAX - fetch user name by mobile
    public function findUser(Request $request)
    {
        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        $wallet = \App\Models\UserWallet::where('user_id', $user->id)->first();
        $balance = $wallet ? $wallet->balance : 0;

        return response()->json([
            'found' => true,
            'name' => $user->name,
            'cin' => $user->cin,
            'balance' => number_format($balance, 2),
        ]);
    }





}
