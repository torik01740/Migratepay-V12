<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get authenticated user's notifications.
     */
    public function index(): JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Store a notification for a target user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'to'      => 'required|email',
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'nullable|string|in:system,success,error,announcement',
        ]);

        $targetUser = User::where('email', strtolower(trim($request->to)))->first();
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $notification = Notification::create([
            'user_id' => $targetUser->id,
            'type'    => $request->type ?? 'system',
            'title'   => $request->title,
            'message' => $request->message,
        ]);

        return response()->json([
            'success'      => true,
            'notification' => $notification,
        ], 201);
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead(): JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
