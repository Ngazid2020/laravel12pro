<?php

namespace App\Notifications;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactRequestReceived extends Notification
{
    use Queueable;

    public function __construct(private ContactRequest $contactRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->contactRequest->sender->name;
        $firstName  = $notifiable->first_name ?: $notifiable->name;

        $mail = (new MailMessage)
            ->subject('Nouvelle demande de mise en relation — Réseau Entrepreneurs')
            ->greeting('Bonjour '.$firstName.' !')
            ->line($senderName.' souhaite établir une mise en relation avec vous sur le Réseau Entrepreneurs Comores.');

        if ($this->contactRequest->message) {
            $mail->line('Message : « '.$this->contactRequest->message.' »');
        }

        return $mail
            ->action('Voir la demande', route('membre.contacts'))
            ->line('Vous pouvez accepter ou refuser cette demande depuis votre espace membre.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'contact_request_id' => $this->contactRequest->id,
            'sender_id'          => $this->contactRequest->sender_id,
            'sender_name'        => $this->contactRequest->sender->name,
            'message'            => $this->contactRequest->message,
        ];
    }
}
