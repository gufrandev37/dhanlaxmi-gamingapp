<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get notifications list
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function show($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $notification
        ]);
    }

}
