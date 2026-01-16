<?php

namespace App\Notifications;

use App\Models\PembayaranKas;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSubmitted extends Notification
{
    use Queueable;

    public function __construct(public PembayaranKas $pembayaran)
    {
    }

    public function via($notifiable)
    {
        return ['database']; // atau ['mail', 'database']
    }

    public function toDatabase($notifiable)
    {
        return [
            'pembayaran_id' => $this->pembayaran->id,
            'amount' => $this->pembayaran->amount,
            'currency' => $this->pembayaran->currency,
            'user_id' => $this->pembayaran->user_id,
            'message' => 'Pembayaran kas baru menunggu verifikasi.',
        ];
    }

    // optional jika pakai email
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pembayaran Kas Baru')
            ->line('Ada pembayaran kas baru.')
            ->action('Lihat Pembayaran', url('/admin/pembayaran-kas'))
            ->line('Silakan verifikasi.');
    }
}
