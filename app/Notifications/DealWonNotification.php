<?php

namespace App\Notifications;

use App\Domain\Deals\Models\Deal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DealWonNotification extends Notification
{
    use Queueable;

    public function __construct(public Deal $deal)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Deal won!',
            'body'  => $this->deal->title . ' has been marked as WON.',
            'icon'  => 'award',
            'url'   => route('deals.edit', $this->deal),
        ];
    }
}
