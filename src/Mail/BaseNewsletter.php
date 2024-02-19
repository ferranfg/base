<?php

namespace Ferranfg\Base\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class BaseNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The notifiable instance.
     *
     * @var mixed
     */
    public $notifiable;

    /**
     * The post instance.
     *
     * @var \App\Models\Post|Ferranfg\Base\Models\Post
     */
    public $post;

    /**
     * The unsubscribe URL.
     *
     * @var string
     */
    public $unsubscribe_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notifiable, $post)
    {
        $this->notifiable = $notifiable;
        $this->post = $post;

        $this->unsubscribe_url = url('newsletter/unsubscribe/' . encrypt($this->notifiable->id));
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            to: $this->notifiable->email,
            subject: $this->post->name,
        );
    }

    /**
     * Get the message headers.
     *
     * @return \Illuminate\Mail\Mailables\Headers
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$this->unsubscribe_url}>",
            ],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'base::newsletter',
            with: [
                'token' => encrypt($this->notifiable->id),
                'post' => $this->post,
            ],
        );
    }
}
