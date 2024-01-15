<?php

namespace Ferranfg\Base\Notifications;

use Ferranfg\Base\Base;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNewsletter extends Notification
{
    use Queueable;

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
        return (new MailMessage)
            ->bcc(Base::$developers)
            ->subject(__('Welcome to :app_name', ['app_name' => config('app.name')]))
            ->line(__("You're all set."))
            ->line(__("Your email is now confirmed and now you're the newest member of the community. Check your email for future messages from us."))
            ->line(__("In the meantime, go ahead and follow us on our social networks."))
            ->action(__("Follow :app_name", ['app_name' => config('app.name')]), config('base.newsletter_action'))
            ->line(__("With that, welcome to the community."))
            ->line(__("Now that you're signed up, you'll start to receive our insanely valuable content right in your inbox."))
            ->line(__("Talk soon."));
    }
}
