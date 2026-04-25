<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|string'
        ]);

        $query = Payment::with('user');

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
        }

        // 📅 Date Filter
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // 🔄 Status Filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(10);

        $totalUsers = User::count();
        $activeUsers = User::where('status',1)->count();
        $inactiveUsers = User::where('status',0)->count();

        return view('admin.payment-history', compact(
            'payments',
            'totalUsers',
            'activeUsers',
            'inactiveUsers'
        ));
    }
}
