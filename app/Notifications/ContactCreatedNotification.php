<?php

namespace App\Notifications;

use App\Domain\Contacts\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Contact $contact)
    {
    }

    public function via($notifiable): array
    {
        return ['database']; // store in DB so we can show in navbar
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'New contact created',
            'body'  => $this->contact->full_name . ' has been added.',
            'icon'  => 'user-plus',
            'url'   => route('contacts.edit', $this->contact),
        ];
    }
}
