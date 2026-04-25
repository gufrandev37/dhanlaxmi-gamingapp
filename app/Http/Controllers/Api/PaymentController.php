<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;


class PaymentController extends Controller
{
    public function payNow(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:50|max:10000',
    ]);

    $payment = Payment::create([
        'user_id' => auth()->id(),
        'amount' => $request->amount,
        'payment_method' => 'online',
        'transaction_id' => 'TXN-' . strtoupper(\Str::random(10)),
        'status' => 'success' 
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Payment successful',
        'data' => $payment
    ]);
}
}
