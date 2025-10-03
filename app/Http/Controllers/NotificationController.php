<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(public NotificationService $service) {}

    public function notifications()
    {
        // getGroupedNotifications(user, number per page)
        [$grouped, $notifications] = $this->service->getGroupedNotifications(Auth::user());

        return view('dashboard.notifications', compact('grouped', 'notifications'));
    }

    public function markAsRead($id)
    {
        $this->service->markRead(Auth::user(), $id);

        return back()->with('success', 'Notification marked as read!');
    }

    public function markAllRead()
    {
        $this->service->markAllRead(Auth::user());

        return back()->with('success', 'All Notification marked as read!');
    }
}
