<?php

namespace dees040\AuthExtra\Notifications;

use Illuminate\Bus\Queueable;
use dees040\AuthExtra\Locator;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifySuspiciousLogin extends Notification
{
    use Queueable;

    /**
     * The Locator instance.
     *
     * @var \dees040\AuthExtra\Locator
     */
    private $location;

    /**
     * Create a new notification instance.
     *
     * @param  \dees040\AuthExtra\Locator  $location
     */
    public function __construct(Locator $location)
    {
        $this->location = $location;
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
        $url = route('verify.user') . '?token=';

        return (new MailMessage)
            ->subject('Suspicious login')
            ->line('Dear ' . $notifiable->name . ',')
            ->line('Somebody tried to login into your account. The login came from:')
            ->line($this->location->getCity() . ', ' .$this->location->getCountry() . ' and the following IP: ' . $this->location->getIp())
            ->action('Secure your account now', $url)
            ->line('Sorry for any inconveniences!');
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