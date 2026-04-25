<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\Game;
use App\Models\GamePlay;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\Winning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    /* =====================================
       DASHBOARD
    ===================================== */
public function dashboard()
    {
        // ── Users ──────────────────────────────────────────────────────
        $totalUsers = User::count();
        $todayUsers = User::whereDate('created_at', today())->count();

        // ── Wallet Balance (current balance held by users) ─────────────
        // Uses UserWallet table's 'balance' column
        $walletAmount = UserWallet::sum('balance') ?? 0;

        // ── Game Stats ─────────────────────────────────────────────────
        $betAmount = GamePlay::sum('amount') ?? 0;
        $winAmount = GamePlay::sum('win_amount') ?? 0;

        // ── Platform P&L ───────────────────────────────────────────────
        $adminEarning = max(0, $betAmount - $winAmount);  // profit
        $lossAmount = max(0, $winAmount - $betAmount);  // loss

        // ── Deposits (from transaction/wallet history table) ───────────
        // ⚠️  If your deposit table is different, change the model name below
        $todayDeposit = Wallet::where('type', 'credit')
            ->whereDate('created_at', today())
            ->sum('amount') ?? 0;

        $totalDeposit = Wallet::where('type', 'credit')
            ->sum('amount') ?? 0;

        // ── Debug: temporarily log values to verify data is coming ─────
        // Remove this block once you confirm data is correct
        \Log::info('Dashboard Stats', [
            'totalUsers' => $totalUsers,
            'todayUsers' => $todayUsers,
            'walletAmount' => $walletAmount,
            'betAmount' => $betAmount,
            'winAmount' => $winAmount,
            'adminEarning' => $adminEarning,
            'lossAmount' => $lossAmount,
            'todayDeposit' => $todayDeposit,
            'totalDeposit' => $totalDeposit,
        ]);

        return view('admin.dashboard', compact(
            'totalUsers',
            'walletAmount',
            'betAmount',
            'winAmount',
            'lossAmount',
            'adminEarning',
            'todayUsers',
            'todayDeposit',
            'totalDeposit',
        ));
    }





    public function usersPage()
    {
        // ✅ Compare against 'active'/'inactive' strings — matches your ENUM column
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();

        return view('admin.users', compact('totalUsers', 'activeUsers', 'inactiveUsers'));
    }


    // ── DataTable data ───────────────────────────────────────────────────
    public function usersData()
    {
        $users = User::select('id', 'cin', 'name', 'phone', 'email', 'status', 'role', 'created_at');

        return DataTables::of($users)
            ->addIndexColumn()

            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y') : '-';
            })

            // ✅ Compare against 'active' string — NOT == 1
            ->editColumn('status', function ($row) {
                return $row->status === 'active'
                    ? '<span class="badge bg-success px-2 py-1">Active</span>'
                    : '<span class="badge bg-danger px-2 py-1">Inactive</span>';
            })

            ->editColumn('role', function ($row) {
                return $row->role ? ucfirst($row->role) : '-';
            })

            // ✅ Button label/color based on 'active'/'inactive' string
            ->addColumn('action', function ($row) {
                $isActive = $row->status === 'active';
                $label = $isActive ? 'Deactivate' : 'Activate';
                $cls = $isActive ? 'btn-warning' : 'btn-success';
                $icon = $isActive ? 'toggle-on' : 'toggle-off';

                return '
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm ' . $cls . ' toggleStatus"
                        data-id="' . $row->id . '">
                        <i class="bi bi-' . $icon . '"></i> ' . $label . '
                    </button>
                    <button class="btn btn-sm btn-danger deleteUser"
                        data-id="' . $row->id . '">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            })

            ->rawColumns(['status', 'role', 'action'])
            ->make(true);
    }


    // ── Toggle status ────────────────────────────────────────────────────
// ✅ Method name: toggleUserStatus — unique, matches route, no conflict
    public function toggleUserStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // ✅ Flip between 'active' and 'inactive' strings
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User status updated to ' . $user->status . '.',
                'status' => $user->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // ── Delete user ──────────────────────────────────────────────────────
// ✅ Method name: destroyUser — matches route
    public function destroyUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // ── Safely delete from related tables ───────────────────────
            // Each table is wrapped separately so one missing table
            // does NOT crash the whole delete operation.
            //
            // ⚠️  Check your actual table names in phpMyAdmin / DB and
            //     update the list below to match exactly.

            $relatedTables = [
                'wallets',      // change if your table is named differently
                'game_plays',   // change if your table is named differently
                'winnings',     // change if your table is named differently
                'withdrawals',  // change if your table is named differently
                'payments',     // add or remove tables as needed
            ];

            foreach ($relatedTables as $table) {
                try {
                    // Only delete if the table actually exists in DB
                    if (\Schema::hasTable($table)) {
                        // Only delete if table has a user_id column
                        if (\Schema::hasColumn($table, 'user_id')) {
                            \DB::table($table)->where('user_id', $id)->delete();
                        }
                    }
                } catch (\Exception $tableError) {
                    // Log the table error but keep going — don't stop the delete
                    \Log::warning("Could not delete from {$table} for user {$id}: " . $tableError->getMessage());
                }
            }

            // ── Delete the user ──────────────────────────────────────────
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
            ]);

        } catch (\Exception $e) {
            // Log the REAL error so you can see it in storage/logs/laravel.log
            \Log::error('destroyUser failed for id ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function create()
    {
        $roles = Role::all();
        return view('admin.add-admin', compact('roles'));
    }

    /* =====================================
       STORE ADMIN
    ===================================== */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'phone' => 'nullable',
            'role_id' => 'required|exists:roles,id',
            'modules' => 'nullable|array',
            'aadhaar_number' => 'nullable|string',
            'pan_number' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:accepted,rejected,pending',
        ]);

        DB::beginTransaction();

        try {

            $imageName = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/admins'), $imageName);
            }

            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
                'aadhaar_number' => $validated['aadhaar_number'] ?? null,
                'pan_number' => $validated['pan_number'] ?? null,
                'driving_license' => $validated['driving_license'] ?? null,
                'image' => $imageName,
                'status' => $validated['status'] ?? 'pending',
            ]);

            DB::commit();

            // ✅ Stay on Add Admin Page (No Route Change)
            return redirect()->route('admin.add')
                ->with('success', 'Admin Created Successfully');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', 'Something went wrong!');
        }
    }

    /* =====================================
       MANAGE ADMIN (Pagination Fixed)
    ===================================== */
    public function winningHistory(Request $request)
    {
        $gameId = $request->game_id;

        // ── shared filter closure (search + date + game) ──────────────
        $applyFilters = function ($q) use ($request, $gameId) {
            $q->when(
                $request->search,
                fn($q) => $q->whereHas(
                    'user',
                    fn($u) => $u->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                )
            )
                ->when($request->from, fn($q) => $q->whereDate('updated_at', '>=', $request->from))
                ->when($request->to, fn($q) => $q->whereDate('updated_at', '<=', $request->to))
                ->when($gameId, fn($q) => $q->where('game_id', $gameId));
        };

        // ── From game_plays (jodi, crossing, copy_paste) ───────────────
        $gamePlays = \App\Models\GamePlay::with('user')
            ->where('status', 'win')
            ->where('is_price_config', false)
            ->tap($applyFilters)
            ->select('id', 'user_id', 'game_id', 'play_type', 'number', 'amount', 'win_amount', 'status', 'updated_at');

        // ── From andar_plays ───────────────────────────────────────────
        $andarPlays = \App\Models\AndarPlay::with('user')
            ->where('status', 'win')
            ->where('is_price_config', false)
            ->tap($applyFilters)
            ->select('id', 'user_id', 'game_id', \DB::raw("'andar' as play_type"), 'number', 'amount', 'win_amount', 'status', 'updated_at');

        // ── From bahar_plays ───────────────────────────────────────────
        $baharPlays = \App\Models\BaharPlay::with('user')
            ->where('status', 'win')
            ->where('is_price_config', false)
            ->tap($applyFilters)
            ->select('id', 'user_id', 'game_id', \DB::raw("'bahar' as play_type"), 'number', 'amount', 'win_amount', 'status', 'updated_at');

        // ── Union + paginate ───────────────────────────────────────────
        $winnings = $gamePlays
            ->union($andarPlays)
            ->union($baharPlays)
            ->latest('updated_at')
            ->paginate(10);

        // ── Manually attach game names after pagination ────────────────
        // (Eloquent with() doesn't work on union results)
        $gameIds = $winnings->pluck('game_id')->filter()->unique();
        $gamesMap = \App\Models\Game::whereIn('id', $gameIds)
            ->pluck('game_name', 'id');   // [ id => game_name ]

        $winnings->each(function ($row) use ($gamesMap) {
            $row->game_name = $gamesMap->get($row->game_id, '-');
        });

        // ── Games list for the dropdown ────────────────────────────────
        $games = \App\Models\Game::orderBy('game_name')->get(['id', 'game_name']);

        // ── Stats (respect all active filters) ────────────────────────
        $totalWinners = $winnings->total();
        $totalWinAmount = \App\Models\GamePlay::where('status', 'win')->where('is_price_config', false)
            ->when($gameId, fn($q) => $q->where('game_id', $gameId))
            ->when($request->from, fn($q) => $q->whereDate('updated_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('updated_at', '<=', $request->to))
            ->sum('win_amount')
            + \App\Models\AndarPlay::where('status', 'win')->where('is_price_config', false)
                ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                ->when($request->from, fn($q) => $q->whereDate('updated_at', '>=', $request->from))
                ->when($request->to, fn($q) => $q->whereDate('updated_at', '<=', $request->to))
                ->sum('win_amount')
            + \App\Models\BaharPlay::where('status', 'win')->where('is_price_config', false)
                ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                ->when($request->from, fn($q) => $q->whereDate('updated_at', '>=', $request->from))
                ->when($request->to, fn($q) => $q->whereDate('updated_at', '<=', $request->to))
                ->sum('win_amount');

        return view('admin.winning-history', compact(
            'winnings',
            'games',
            'totalWinners',
            'totalWinAmount'
        ));
    }

    public function manageAdmin()
    {
        $admins = Admin::orderBy('id', 'desc')->paginate(10);
        return view('admin.manage-admin', compact('admins'));
    }

    /* =====================================
       UPDATE STATUS
    ===================================== */

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected,pending'
        ]);

        $admin = Admin::findOrFail($id);
        $admin->status = $request->status;
        $admin->save();

        return back()->with('success', 'Status updated successfully.');
    }

    /* =====================================
       CHANGE PASSWORD
    ===================================== */

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['Current password is incorrect']);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'Password updated successfully');
    }
    public function getAdminDetails($id)
    {
        $admin = \App\Models\Admin::findOrFail($id);
        return response()->json($admin);
    }
    public function destroy($id)
    {
        try {

            $user = \App\Models\User::findOrFail($id);

            // Delete dependent records first
            \DB::table('games')->where('user_id', $id)->delete();
            \DB::table('wallets')->where('user_id', $id)->delete();
            \DB::table('winnings')->where('user_id', $id)->delete();
            \DB::table('withdraws')->where('user_id', $id)->delete();
            \DB::table('payments')->where('user_id', $id)->delete();

            $user->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            abort(404, 'Admin not found');
        }

        // ✅ Validate image (minimal addition)
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ If new image uploaded
        if ($request->hasFile('image')) {

            // Delete old image (important)
            if ($admin->image && file_exists(public_path('uploads/admins/' . $admin->image))) {
                unlink(public_path('uploads/admins/' . $admin->image));
            }

            $file = $request->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/admins'), $imageName);

            $admin->image = $imageName;
        }

        // Update other fields
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->aadhaar_number = $request->aadhaar_number;
        $admin->pan_number = $request->pan_number;
        $admin->driving_license = $request->driving_license;

        $admin->save();

        return redirect()->route('admin.manage')
            ->with('success', 'Admin updated successfully');
    }







    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status updated.');
    }



    /* =====================================
   EDIT ADMIN (for modal pre-fill)
===================================== */


    /* =====================================
       UPDATE ADMIN
    ===================================== */
    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'driving_license' => 'nullable|string|max:50',
            'status' => 'required|in:accepted,rejected,pending',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'aadhaar_number' => $request->aadhaar_number,
                'pan_number' => $request->pan_number,
                'driving_license' => $request->driving_license,
                'status' => $request->status,
                'role_id' => $request->role_id,
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($admin->image && file_exists(public_path('uploads/admins/' . $admin->image))) {
                    unlink(public_path('uploads/admins/' . $admin->image));
                }
                $file = $request->file('image');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/admins'), $imageName);
                $data['image'] = $imageName;
            }

            $admin->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /* =====================================
       DELETE ADMIN
    ===================================== */
    public function destroyAdmin($id)
    {
        try {
            $admin = Admin::findOrFail($id);

            // 🔒 Prevent deleting main Super Admin
            if ($admin->email === 'superadmin@admin.com') {
                return back()->with('error', 'Super Admin cannot be deleted.');
            }

            // Delete image file
            if ($admin->image && file_exists(public_path('uploads/admins/' . $admin->image))) {
                unlink(public_path('uploads/admins/' . $admin->image));
            }

            $admin->delete();

            return back()->with('success', 'Admin deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }


}
