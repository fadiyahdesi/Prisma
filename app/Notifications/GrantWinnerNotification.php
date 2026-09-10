<?php

namespace App\Notifications;

use App\Models\PpmKontrak;
use App\Models\PpmUsulan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GrantWinnerNotification extends Notification
{
    use Queueable;

    public function __construct(public PpmUsulan $usulan, public PpmKontrak $kontrak)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'grant_winner',
            'usulan_id' => $this->usulan->id,
            'kontrak_id' => $this->kontrak->id,
            'nomor_sk' => $this->kontrak->nomor_sk,
            'title' => 'Selamat! Usulan Anda Ditetapkan Sebagai Pemenang Hibah',
            'message' => "Usulan '{$this->usulan->judul_usulan}' telah resmi ditetapkan sebagai pemenang hibah dengan SK {$this->kontrak->nomor_sk}. Silakan tanda tangani kontrak SPK digital dan unggah buku tabungan Anda.",
            'url' => route('pengusul.kontrak.index'),
        ];
    }
}

