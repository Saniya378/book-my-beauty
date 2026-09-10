<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $userId = Auth::id();

        if ($request->has('mark_read')) {
            Notification::where('user_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);
            return response()->json(['success' => true]);
        }

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead()
    {
        if (Auth::check()) {
            Notification::where('user_id', Auth::id())->where('is_read', 0)->update(['is_read' => 1]);
        }
        return response()->json(['success' => true]);
    }
}
