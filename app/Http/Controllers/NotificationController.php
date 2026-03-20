<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Determine recipient type and ID from the current route/guard.
     */
    protected function getRecipient(): array
    {
        if (Auth::guard('parent')->check()) {
            return ['parent', Auth::guard('parent')->id()];
        }
        if (Auth::guard('midwife')->check()) {
            return ['midwife', Auth::guard('midwife')->id()];
        }
        abort(403, 'Unauthorized');
        return ['', 0]; // unreachable, satisfies static analysis
    }

    /**
     * List notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        [$type, $id] = $this->getRecipient();

        $notifications = $this->notificationService->getNotifications($type, $id, 30);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 1,
                'data' => $notifications,
            ]);
        }

        return view('back.pages.' . $type . '.notifications', [
            'pageTitle' => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, $id)
    {
        [$type, $recipientId] = $this->getRecipient();

        $this->notificationService->markAsRead($id, $type, $recipientId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 1, 'msg' => 'Notification marked as read.']);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount()
    {
        [$type, $id] = $this->getRecipient();

        return response()->json([
            'status' => 1,
            'count' => $this->notificationService->getUnreadCount($type, $id),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        [$type, $id] = $this->getRecipient();

        $count = $this->notificationService->markAllRead($type, $id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 1, 'msg' => "{$count} notifications marked as read."]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
