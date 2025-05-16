<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('Reset Password - Prime Billing Application')
            ->view('emails.reset-password', [
                'token' => $this->token,
                'notifiable' => $notifiable,
                'logo_url' => config('app.url') . '/images/logo-kpu-ls.png',
            ]);
    }
}
