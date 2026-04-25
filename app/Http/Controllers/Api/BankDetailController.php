<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankDetail;


class BankDetailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code'      => 'required|string|max:20',
        ]);

        $user = $request->user(); // authenticated user

        // Create or update bank details
        $bankDetail = BankDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name'      => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code'      => $request->ifsc_code,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bank details saved successfully',
            'data'    => $bankDetail,
        ], 200);
    }

        public function show(Request $request)
    {
        $bankDetail = $request->user()->bankDetail;

        if (!$bankDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Bank details not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $bankDetail,
        ]);
    }




}
