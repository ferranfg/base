<?php

namespace Ferranfg\Base\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use SnoerenDevelopment\DiscordWebhook\DiscordMessage;
use SnoerenDevelopment\DiscordWebhook\DiscordWebhookChannel;

class DiscordNotification extends Notification
{
    use Queueable;

    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordWebhookChannel::class];
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return (new DiscordMessage)->content($this->content);
    }

}
