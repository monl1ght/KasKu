<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class PendingEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $pendingId,
        public string $name,
        public string $email,
        public string $token,
        public Carbon $expiresAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'pending.verification.verify',
            $this->expiresAt,
            [
                'pending' => $this->pendingId,
                'token'   => $this->token,
            ]
        );

        return (new MailMessage)
            ->subject('Verifikasi Email - KasKu')
            ->greeting("Halo, {$this->name} 👋")
            ->line('Terima kasih sudah mendaftar di KasKu.')
            ->line('Klik tombol di bawah untuk mengaktifkan akunmu. Akun baru akan dibuat SETELAH kamu klik link ini.')
            ->action('Verifikasi Email', $url)
            ->line('Jika kamu tidak merasa mendaftar, abaikan email ini.')
            ->line('Link ini akan kedaluwarsa sesuai batas waktu.');
    }
}
