<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ActivityReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $overdueCount,
        public int $todayCount
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // add 'mail' later if you like
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'activity_reminder',
            'overdue_count' => $this->overdueCount,
            'today_count'   => $this->todayCount,
        ];
    }
}
