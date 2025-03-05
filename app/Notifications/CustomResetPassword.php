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

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage) // Harus pakai return
    //         ->subject('Reset Password - Your Custom App Name')
    //         ->greeting('Hello, ' . $notifiable->name . '!')
    //         ->line('<img src="https://lh3.googleusercontent.com/proxy/-AksYFOPP1Xz85_TSkklmUYBIqSy97KfcDVIkM3ZLFf2rto5JlfbwvWVnLdL18hEoAkoU2Y5ZnFE5SsNZOwBQuMlVmmNE0o32r90WrYdGNx-bThwFTMKxi-4g60Po-UMxDhzTTSk8MlUaHFp_8pi" width="150">') // Gunakan gambar dari CDN
    //         ->line('Klik tombol di bawah ini untuk mereset password Anda:')
    //         ->action('Reset Password', url(config('app.url') . '/reset-password?token=' . $this->token))
    //         ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
    //         ->line('<strong>Terima kasih telah menggunakan layanan kami!</strong>')
    //         ->salutation('Salam, Tim Support');
    // }
    public function toMail($notifiable)
{
    
    return (new MailMessage)
        ->subject('Reset Password - Karya Invoice Application')
        ->view('emails.reset-password', [
            'token' => $this->token,
            'notifiable' => $notifiable,
            'logo_url' => 'https://lh3.googleusercontent.com/proxy/-AksYFOPP1Xz85_TSkklmUYBIqSy97KfcDVIkM3ZLFf2rto5JlfbwvWVnLdL18hEoAkoU2Y5ZnFE5SsNZOwBQuMlVmmNE0o32r90WrYdGNx-bThwFTMKxi-4g60Po-UMxDhzTTSk8MlUaHFp_8pi'
        ]);
}
}
