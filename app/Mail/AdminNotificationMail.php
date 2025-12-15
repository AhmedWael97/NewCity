<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $eventData;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $eventData = [])
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->eventData = $eventData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.admin-notification')
                    ->with([
                        'body' => $this->body,
                        'eventData' => $this->eventData,
                    ]);
    }
}
