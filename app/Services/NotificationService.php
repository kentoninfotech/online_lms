<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Groups Notification by category
     */
    public function getGroupedNotifications($user, $perPage = 30)
    {
        $notifications = $user->notifications()->latest()->paginate($perPage);

        $grouped = $notifications->getCollection()->groupBy(function ($note) {
            if (str_contains($note->type, 'Payment')) {
                return 'Payments';
            }
            if (str_contains($note->type, 'Reschedule')) {
                return 'Reschedules';
            }
            if (str_contains($note->type, 'ClassReminde') || str_contains($note->type, 'Lesson')) {
                return 'Classes';
            }
            return 'Others';
        });

        return [$grouped, $notifications];
    }

    /**
     * Mark notification as read
     */
    public function markRead($user, $id)
    {
        $note = $user->notifications()->findOrFail($id);
        $note->markAsRead();
    }

    /**
     * Mark all notification as read
     */
    public function markAllRead($user)
    {
        $user->unreadNotifications->markAsRead();
    }
}
