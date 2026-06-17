<?php

namespace App\Notifications;

use App\Models\MentoringRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentoringRequestReviewed extends Notification
{
    use Queueable;

    public function __construct(private MentoringRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isApproved = $this->request->status === 'approved';
        $firstName  = $notifiable->first_name ?: $notifiable->name;
        $mentorName = $this->request->mentor->name;

        $mail = (new MailMessage)
            ->subject($isApproved
                ? 'Votre demande de mentorat a été approuvée — Réseau Entrepreneurs'
                : 'Votre demande de mentorat — Réseau Entrepreneurs')
            ->greeting('Bonjour '.$firstName.' !');

        if ($isApproved) {
            $mail->line($mentorName.' a été assigné comme votre mentor sur le Réseau Entrepreneurs Comores.')
                 ->line('Vous pouvez maintenant consulter vos sessions de mentorat depuis votre espace membre.')
                 ->action('Accéder à ma page mentorat', route('membre.mentoring'));
        } else {
            $mail->line('Votre demande de mentorat auprès de '.$mentorName.' n\'a pas pu être validée.')
                 ->line('Vous pouvez soumettre une nouvelle demande en choisissant un autre mentor.')
                 ->action('Faire une nouvelle demande', route('membre.mentoring'));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'mentoring_request_reviewed',
            'status'      => $this->request->status,
            'mentor_name' => $this->request->mentor->name,
        ];
    }
}
