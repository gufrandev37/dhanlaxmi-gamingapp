<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Notification::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })

                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary editBtn"
                        data-update-url="' . route('admin.notification.update', $row->id) . '"
                        data-title="' . e($row->title) . '"
                        data-message="' . e($row->message) . '">
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger deleteBtn"
                        data-delete-url="' . route('admin.notification.delete', $row->id) . '">
                        Delete
                    </button>
                ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.notification');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Notification::create([
            'title' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return redirect()
            ->route('admin.notification')
            ->with('success', 'Notification sent successfully.');
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        $notification->update([
            'title' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()
            ->route('admin.notification')
            ->with('success', 'Notification updated successfully');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()
            ->route('admin.notification')
            ->with('success', 'Notification deleted successfully');
    }
}

