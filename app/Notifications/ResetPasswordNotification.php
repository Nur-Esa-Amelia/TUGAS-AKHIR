<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Token reset password.
     *
     * @var string
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Permintaan Reset Password - Sistem EWS IKU')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna') . '!')
            ->line('Anda menerima email ini karena kami menerima permintaan untuk mengatur ulang (reset) password akun Anda di Sistem Early Warning IKU/IKT Politeknik Sukabumi.')
            ->action('Reset Password Saya', $url)
            ->line('Tautan reset password ini akan kadaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini dan kata sandi Anda tidak akan berubah.')
            ->salutation('Salam hangat,' . "\n" . 'Tim EWS IKU Politeknik Sukabumi');
    }
}
