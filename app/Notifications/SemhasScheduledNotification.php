<?php

namespace App\Notifications;

use App\Models\PpmSeminarHasil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SemhasScheduledNotification extends Notification
{
    use Queueable;

    public function __construct(public PpmSeminarHasil $semhas)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $usulan = $this->semhas->usulan;
        $formattedDate = $this->semhas->jadwal_seminar ? $this->semhas->jadwal_seminar->isoFormat('dddd, D MMMM Y - HH:mm') : '-';

        return [
            'type' => 'semhas_scheduled',
            'usulan_id' => $usulan->id,
            'semhas_id' => $this->semhas->id,
            'jadwal' => $formattedDate,
            'ruangan' => $this->semhas->ruangan_or_link,
            'title' => 'Jadwal Seminar Hasil (Semhas) Ditetapkan',
            'message' => "Seminar Hasil untuk usulan '{$usulan->judul_usulan}' dijadwalkan pada {$formattedDate} di {$this->semhas->ruangan_or_link}.",
            'url' => route('pengusul.laporan-akhir.index', $usulan->id),
        ];
    }
}

