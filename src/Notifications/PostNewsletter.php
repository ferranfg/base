<?php

namespace Ferranfg\Base\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostNewsletter extends Notification
{
    use Queueable;

    private $post;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
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
        $token = encrypt($notifiable->id);
        $unsubscribe_url = url("newsletter/unsubscribe/{$token}");

        return (new MailMessage)
            ->subject($this->post->name)
            ->markdown('base::newsletter', [
                'unsubscribe_url' => $unsubscribe_url,
                'post' => $this->post,
            ])
            ->withSwiftMessage(function ($message) use ($unsubscribe_url)
            {
                $message->getHeaders()->addTextHeader('List-Unsubscribe', $unsubscribe_url);
            });
    }
}
