<?php

namespace App\Notifications;

use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisbursementTermin2Notification extends Notification
{
    use Queueable;

    public function __construct(public PpmKontrak $kontrak, public PpmPencairanDana $pencairan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'dana_cair_termin_2',
            'kontrak_id' => $this->kontrak->id,
            'pencairan_id' => $this->pencairan->id,
            'jumlah_dana' => $this->pencairan->jumlah_dana,
            'nomor_referensi' => $this->pencairan->nomor_referensi,
            'title' => 'Pelunasan Hibah! Dana Termin II (30%) Telah Ditransfer',
            'message' => "Dana pelunasan Termin II sebesar Rp " . number_format($this->pencairan->jumlah_dana, 0, ',', '.') . " untuk usulan '{$this->kontrak->usulan->judul_usulan}' telah ditransfer. Hibah Anda resmi berstatus Selesai (Completed).",
            'url' => route('pengusul.kontrak.index'),
        ];
    }
}

