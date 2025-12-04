<?php

namespace App\Notifications;

use App\Domain\Deals\Models\Deal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DealWonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Deal $deal)
    {
    }

    public function via($notifiable): array
    {
        return ['database']; // add 'mail' later if desired
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'        => 'deal_won',
            'deal_id'     => $this->deal->id,
            'title'       => $this->deal->title,
            'amount'      => $this->deal->amount,
            'currency'    => $this->deal->currency,
            'status'      => $this->deal->status,
            'pipeline_id' => $this->deal->pipeline_id,
            'stage_id'    => $this->deal->stage_id,
        ];
    }
}
