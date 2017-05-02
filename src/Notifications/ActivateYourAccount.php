<?php

namespace dees040\AuthExtra\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ActivateYourAccount extends Notification
{
    use Queueable;

    /**
     * The token to send to the user.
     *
     * @var string
     */
    private $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('activation.email').'?token='.$this->token;

        return (new MailMessage)
            ->subject('Activate your account')
            ->line('Dear '.$notifiable->name.',')
            ->line('Please activate your account by clicking on the following button.')
            ->action('Activate your account', $url)
            ->line('Thank you for choosing us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
