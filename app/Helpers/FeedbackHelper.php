<?php

namespace App\Helpers;

use App\Models\ContactMessage;

class FeedbackHelper
{
    /**
     * Get count of unread contact messages
     */
    public static function getUnreadCount(): int
    {
        return ContactMessage::unread()->count();
    }
}
