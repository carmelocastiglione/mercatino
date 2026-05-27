<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Display a listing of the student's notifications.
     */
    public function index(): View
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $user->notifications()->unread()->count();

        return view('student.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Get unread notification count (JSON endpoint for badge).
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->notifications()->unread()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        // Authorize the user
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Non sei autorizzato ad accedere a questa notifica');
        }

        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $user = auth()->user();
        $user->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Tutte le notifiche sono state marcate come lette');
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification): RedirectResponse
    {
        // Authorize the user
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Non sei autorizzato ad accedere a questa notifica');
        }

        $notification->delete();

        return back();
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(): RedirectResponse
    {
        $user = auth()->user();
        $user->notifications()
            ->read()
            ->delete();

        return back()->with('success', 'Le notifiche lette sono state eliminate');
    }
}
