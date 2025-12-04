<?php

namespace App\Observers;

use App\Domain\Deals\Models\Deal; // adjust if needed
use App\Notifications\DealCreatedNotification;
use App\Notifications\DealWonNotification;
use App\Domain\Activities\Models\Activity;
use Carbon\Carbon;

class DealObserver
{
    public function created(Deal $deal): void
    {
        if ($deal->owner) {
            $deal->owner->notify(new DealCreatedNotification($deal));
        }
    }

    public function updated(Deal $deal): void
    {
        if ($deal->wasChanged('status')) {
            $old = $deal->getOriginal('status');
            $new = $deal->status;

            if ($old !== 'won' && $new === 'won' && $deal->owner) {
                $deal->owner->notify(new DealWonNotification($deal));

                Activity::create([
                    'account_id'   => $deal->account_id,
                    'tenant_id'    => $deal->tenant_id,
                    'subject_type' => get_class($deal),
                    'subject_id'   => $deal->id,
                    'owner_id'     => $deal->owner_id,
                    'type'         => 'task',
                    'title'        => 'Deal won: '.$deal->title,
                    'notes'        => 'Automatically logged when the deal was marked WON.',
                    'due_date'     => Carbon::now(),
                    'is_completed' => true,
                ]);
            }
        }
    }
}
