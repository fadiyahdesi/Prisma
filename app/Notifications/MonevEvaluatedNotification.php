<?php

namespace App\Notifications;

use App\Models\PpmMonevKemajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MonevEvaluatedNotification extends Notification
{
    use Queueable;

    public function __construct(public PpmMonevKemajuan $monev)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $usulan = $this->monev->usulan;

        return [
            'type' => 'monev_evaluated',
            'usulan_id' => $usulan->id,
            'monev_id' => $this->monev->id,
            'skor_monev' => $this->monev->skor_monev,
            'rekomendasi' => $this->monev->rekomendasi,
            'title' => 'Hasil Evaluasi Monev Kemajuan (70%) Telah Terbit',
            'message' => "Monev usulan '{$usulan->judul_usulan}' telah dinilai dengan skor {$this->monev->skor_monev} dan rekomendasi: {$this->monev->rekomendasi}.",
            'url' => route('pengusul.monev.index', $usulan->id),
        ];
    }
}

