<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GamePlay;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\User;

class DuplicateBidController extends Controller
{
    /**
     * Show all duplicate bids on ONE page
     */
    public function index()
    {
        $duplicates = GamePlay::with(['user', 'game'])
            ->select(
                'user_id',
                'game_id',
                'play_type',
                'number',
                DB::raw('COUNT(*) as total_bids'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('GROUP_CONCAT(id ORDER BY id ASC) as bid_ids')
            )
            ->where('status', 'pending')
            ->groupBy('user_id', 'game_id', 'play_type', 'number')
            ->having('total_bids', '>', 1)
            ->orderBy('game_id')
            ->get();

        // ONE PAGE VIEW
        return view('admin.duplicate-bids', compact('duplicates'));
    }

    /**
     * Change bid number
     */
    public function changeNumber(Request $request, $id)
    {
        $request->validate([
            'number' => 'required|integer|min:0|max:99'
        ]);

        GamePlay::where('id', $id)->update([
            'number' => $request->number
        ]);

        return redirect()->back()->with('success', 'Bid number changed successfully');
    }

    /**
     * Delete duplicate bid
     */
    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $play = GamePlay::findOrFail($id);

            // ✅ Refund amount to wallet
            Wallet::create([
                'user_id' => $play->user_id,
                'cin' => $play->user->cin ?? null,
                'amount' => $play->amount,
                'type' => 'credit',
            ]);

            // ✅ Delete play
            $play->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Duplicate bid deleted and amount refunded successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function addNewBid(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'game_id' => 'required|exists:games,id',
        'play_type' => 'required|in:jodi,crossing,copy_paste',
        'number' => 'required|integer|min:0|max:99',
        'amount' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {

        $user = User::findOrFail($request->user_id);

        // 🔥 Check duplicate
        $exists = GamePlay::where([
            'user_id' => $request->user_id,
            'game_id' => $request->game_id,
            'play_type' => $request->play_type,
            'number' => $request->number,
            'status' => 'pending',
        ])->exists();

        if ($exists) {
            return back()->with('error', 'This number already exists for this user.');
        }

        // 🔥 Calculate balance
        $credit = Wallet::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        $debit = Wallet::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        $balance = $credit - $debit;

        if ($balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // ✅ Deduct wallet
        Wallet::create([
            'user_id' => $user->id,
            'cin' => $user->cin ?? null,
            'amount' => $request->amount,
            'type' => 'debit',
        ]);

        // ✅ Create bid
        GamePlay::create([
            'user_id' => $request->user_id,
            'game_id' => $request->game_id,
            'play_type' => $request->play_type,
            'number' => $request->number,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        DB::commit();

        return back()->with('success', 'New bid added and wallet deducted successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', 'Something went wrong.');
    }
}

// cancel a particulat
}