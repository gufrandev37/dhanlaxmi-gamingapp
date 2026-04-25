<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamePlay;
use App\Models\AndarPlay;
use App\Models\BaharPlay;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

class BidChartController extends Controller
{
    public function bidsPage(Request $request)
    {
        $games        = Game::all();
        $selectedGame = $request->game_id;
        $selectedDate = $request->date; // e.g. "2025-01-15"

        // Which number is selected per section
        $selectedNum      = $request->number;
        $selectedPlayType = $request->play_type ?? 'jodi';
        $selectedAndarNum = $request->andar_number;
        $selectedBaharNum = $request->bahar_number;

        // ─── Helper: apply common filters (game + date) ──────────
        // NOTE: we EXCLUDE 'cancelled' from all grid counts & header stats
        $baseGame = fn($q) => $q
            ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
            ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
            ->whereNotIn('status', ['cancelled']);

        // ─── KPI cards ───────────────────────────────────────────
        $totalBids   = GamePlay::whereNotIn('status', ['cancelled'])->count();
        $totalAmount = GamePlay::whereNotIn('status', ['cancelled'])->sum('amount');
        $todayBids   = GamePlay::whereDate('created_at', today())->whereNotIn('status', ['cancelled'])->count();
        $todayAmount = GamePlay::whereDate('created_at', today())->whereNotIn('status', ['cancelled'])->sum('amount');

        // ─── JODI Grid ───────────────────────────────────────────
        $jodiCounts = GamePlay::where('play_type', 'jodi')
            ->tap($baseGame)
            ->selectRaw('number, COUNT(*) as total_bids, SUM(amount) as total_amount')
            ->groupBy('number')
            ->get()->keyBy('number');

        // ─── CROSSING Grid ───────────────────────────────────────
        $crossingCounts = GamePlay::where('play_type', 'crossing')
            ->tap($baseGame)
            ->selectRaw('number, COUNT(*) as total_bids, SUM(amount) as total_amount')
            ->groupBy('number')
            ->get()->keyBy('number');

        // ─── COPY PASTE Grid ─────────────────────────────────────
        $copyPasteCounts = GamePlay::where('play_type', 'copy_paste')
            ->tap($baseGame)
            ->selectRaw('number, COUNT(*) as total_bids, SUM(amount) as total_amount')
            ->groupBy('number')
            ->get()->keyBy('number');

        // ─── Selected number detail (GamePlay) ───────────────────
        $bids             = collect();
        $selectedAmount   = 0;
        $selectedBidCount = 0;

        if ($selectedNum !== null) {
            // Detail table shows ALL statuses so admin can see cancelled ones too
            $bids = GamePlay::with(['user', 'game'])
                ->where('number', $selectedNum)
                ->where('play_type', $selectedPlayType)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->latest()
                ->paginate(20, ['*'], 'jodi_page')
                ->withQueryString();

            // Header stats: only non-cancelled
            $selectedAmount = GamePlay::where('number', $selectedNum)
                ->where('play_type', $selectedPlayType)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->sum('amount');

            $selectedBidCount = GamePlay::where('number', $selectedNum)
                ->where('play_type', $selectedPlayType)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->count();
        }

        // ─── ANDAR Grid ──────────────────────────────────────────
        $andarCounts = AndarPlay::tap($baseGame)
            ->selectRaw('number, COUNT(*) as total_bids, SUM(amount) as total_amount')
            ->groupBy('number')->get()->keyBy('number');

        $andarBids             = collect();
        $selectedAndarAmount   = 0;
        $selectedAndarBidCount = 0;

        if ($selectedAndarNum !== null) {
            $andarBids = AndarPlay::with(['user', 'game'])
                ->where('number', $selectedAndarNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->latest()
                ->paginate(20, ['*'], 'andar_page')
                ->withQueryString();

            $selectedAndarAmount = AndarPlay::where('number', $selectedAndarNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->sum('amount');

            $selectedAndarBidCount = AndarPlay::where('number', $selectedAndarNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->count();
        }

        // ─── BAHAR Grid ──────────────────────────────────────────
        $baharCounts = BaharPlay::tap($baseGame)
            ->selectRaw('number, COUNT(*) as total_bids, SUM(amount) as total_amount')
            ->groupBy('number')->get()->keyBy('number');

        $baharBids             = collect();
        $selectedBaharAmount   = 0;
        $selectedBaharBidCount = 0;

        if ($selectedBaharNum !== null) {
            $baharBids = BaharPlay::with(['user', 'game'])
                ->where('number', $selectedBaharNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->latest()
                ->paginate(20, ['*'], 'bahar_page')
                ->withQueryString();

            $selectedBaharAmount = BaharPlay::where('number', $selectedBaharNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->sum('amount');

            $selectedBaharBidCount = BaharPlay::where('number', $selectedBaharNum)
                ->when($selectedGame, fn($q) => $q->where('game_id', $selectedGame))
                ->when($selectedDate,  fn($q) => $q->whereDate('created_at', $selectedDate))
                ->whereNotIn('status', ['cancelled'])
                ->count();
        }

        return view('admin.bid_chart', compact(
            'games', 'selectedGame', 'selectedDate',
            'totalBids', 'totalAmount', 'todayBids', 'todayAmount',
            'jodiCounts', 'crossingCounts', 'copyPasteCounts',
            'bids', 'selectedNum', 'selectedPlayType', 'selectedAmount', 'selectedBidCount',
            'andarCounts', 'andarBids',
            'selectedAndarNum', 'selectedAndarAmount', 'selectedAndarBidCount',
            'baharCounts', 'baharBids',
            'selectedBaharNum', 'selectedBaharAmount', 'selectedBaharBidCount'
        ));
    }

    public function cancelBid(Request $request, string $type, int $id)
    {
        $modelMap = [
            'game'  => GamePlay::class,
            'andar' => AndarPlay::class,
            'bahar' => BaharPlay::class,
        ];

        abort_unless(array_key_exists($type, $modelMap), 404);

        DB::beginTransaction();

        try {
            $bid = $modelMap[$type]::with('user')->lockForUpdate()->findOrFail($id);

            if ($bid->status !== 'pending') {
                return back()->with('error', 'Only pending bids can be cancelled.');
            }

            $bid->status = 'cancelled';
            $bid->save();

            if ($bid->user) {
                $userWallet = UserWallet::where('user_id', $bid->user_id)
                    ->lockForUpdate()->first();

                if (!$userWallet) {
                    $userWallet = UserWallet::create([
                        'user_id' => $bid->user_id,
                        'balance' => 0,
                    ]);
                }

                $userWallet->increment('balance', $bid->amount);

                Wallet::create([
                    'user_id' => $bid->user_id,
                    'cin'     => $bid->user->cin ?? null,
                    'amount'  => $bid->amount,
                    'type'    => 'credit',
                ]);
            }

            DB::commit();

            return back()->with(
                'success',
                "Bid #{$id} cancelled and ₹" . number_format($bid->amount) . " refunded successfully."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}