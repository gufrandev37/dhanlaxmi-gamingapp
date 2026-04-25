<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GamePlay;
use App\Models\AndarPlay;
use App\Models\BaharPlay;
use App\Models\GamePrice;
use App\Models\Game;
use App\Models\UserWallet;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AdminGameResultController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::latest()
            ->when($request->date, fn($q) =>
                $q->whereDate('created_at', $request->date)
            )
            ->paginate(20);

        foreach ($games as $game) {
            $results = [];

            // ✅ FIX: Read directly from correct_answer column on the Game.
            // Previously this queried play tables for status='win' records —
            // but if nobody bet on the winning number, no row has status='win'
            // so nothing showed. correct_answer is always saved on declare.
            if (!empty($game->correct_answer)) {
                $winNumber = $game->correct_answer;

                $results['jodi']  = $winNumber;               // full "89"
                $results['andar'] = substr($winNumber, 0, 1); // "8"
                $results['bahar'] = substr($winNumber, 1, 1); // "9"
            }

            $game->results = $results;
        }

        return view('admin.game-result', compact('games'));
    }

    /**
     * Single declaration:
     *   win_number = "05"
     *   → Jodi  wins on "05"  (full 2-digit number)
     *   → Andar wins on "0"   (first digit)
     *   → Bahar wins on "5"   (last digit)
     */
    public function declare(Request $request)
    {
        $request->validate([
            'game_id'    => 'required|exists:games,id',
            'win_number' => 'required|string|size:2|regex:/^[0-9]{2}$/',
        ]);

        $gameId    = $request->game_id;
        $winNumber = $request->win_number;
        $andarNum  = substr($winNumber, 0, 1);
        $baharNum  = substr($winNumber, 1, 1);

        $jodiPrice  = (float) optional(GamePrice::where('game_type', 'jodi') ->first())->price;
        $andarPrice = (float) optional(GamePrice::where('game_type', 'andar')->first())->price;
        $baharPrice = (float) optional(GamePrice::where('game_type', 'bahar')->first())->price;

        DB::beginTransaction();

        try {
            $winnersCount = 0;
            $totalPaid    = 0;

            // ── 1. JODI ───────────────────────────────────────────────
            $jodiWinners = GamePlay::where('game_id', $gameId)
                ->whereIn('play_type', ['jodi', 'crossing', 'copy_paste'])
                ->where('is_price_config', false)
                ->where('number', $winNumber)
                ->get();

            foreach ($jodiWinners as $bid) {
                $winAmount = $bid->amount * $jodiPrice;
                $bid->update(['win_amount' => $winAmount, 'status' => 'win']);
                $this->creditWallet($bid->user_id, $winAmount);
                $winnersCount++;
                $totalPaid += $winAmount;
            }

            GamePlay::where('game_id', $gameId)
                ->whereIn('play_type', ['jodi', 'crossing', 'copy_paste'])
                ->where('is_price_config', false)
                ->where('number', '!=', $winNumber)
                ->update(['status' => 'lose', 'win_amount' => 0]);

            // ── 2. ANDAR ──────────────────────────────────────────────
            $andarWinners = AndarPlay::where('game_id', $gameId)
                ->where('is_price_config', false)
                ->where('number', $andarNum)
                ->get();

            foreach ($andarWinners as $bid) {
                $winAmount = $bid->amount * $andarPrice;
                $bid->update(['win_amount' => $winAmount, 'status' => 'win']);
                $this->creditWallet($bid->user_id, $winAmount);
                $winnersCount++;
                $totalPaid += $winAmount;
            }

            AndarPlay::where('game_id', $gameId)
                ->where('is_price_config', false)
                ->where('number', '!=', $andarNum)
                ->update(['status' => 'lose', 'win_amount' => 0]);

            // ── 3. BAHAR ──────────────────────────────────────────────
            $baharWinners = BaharPlay::where('game_id', $gameId)
                ->where('is_price_config', false)
                ->where('number', $baharNum)
                ->get();

            foreach ($baharWinners as $bid) {
                $winAmount = $bid->amount * $baharPrice;
                $bid->update(['win_amount' => $winAmount, 'status' => 'win']);
                $this->creditWallet($bid->user_id, $winAmount);
                $winnersCount++;
                $totalPaid += $winAmount;
            }

            BaharPlay::where('game_id', $gameId)
                ->where('is_price_config', false)
                ->where('number', '!=', $baharNum)
                ->update(['status' => 'lose', 'win_amount' => 0]);

            // ── Close game & save correct_answer ──────────────────────
            Game::where('id', $gameId)->update([
                'correct_answer' => $winNumber,
                'status'         => 'close',
            ]);

            DB::commit();

            return back()->with('success',
                "Result Declared! Number: {$winNumber} | Jodi: {$winNumber} | Andar: {$andarNum} | Bahar: {$baharNum} | Winners: {$winnersCount} | Paid: ₹{$totalPaid}"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    private function creditWallet(int $userId, float $amount): void
    {
        $wallet = UserWallet::where('user_id', $userId)->first();
        if ($wallet) {
            $wallet->increment('balance', $amount);
        }

        Wallet::create([
            'user_id' => $userId,
            'amount'  => $amount,
            'type'    => 'credit',
        ]);
    }
}