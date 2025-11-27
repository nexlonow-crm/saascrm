<?php

namespace App\Notifications;

use App\Domain\Deals\Models\Deal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DealCreatedNotification extends Notification
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
            'title' => 'New deal created',
            'body'  => $this->deal->title . ' has been created.',
            'icon'  => 'dollar-sign',
            'url'   => route('deals.edit', $this->deal),
        ];
    }
}
