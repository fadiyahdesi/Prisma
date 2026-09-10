<?php

namespace App\Notifications;

use App\Models\PpmKontrak;
use App\Models\PpmPencairanDana;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisbursementTermin1Notification extends Notification
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
            'type' => 'dana_cair_termin_1',
            'kontrak_id' => $this->kontrak->id,
            'pencairan_id' => $this->pencairan->id,
            'jumlah_dana' => $this->pencairan->jumlah_dana,
            'nomor_referensi' => $this->pencairan->nomor_referensi,
            'title' => 'Dana Hibah Termin I (70%) Telah Ditransfer',
            'message' => "Dana awal sebesar Rp " . number_format($this->pencairan->jumlah_dana, 0, ',', '.') . " telah ditransfer ke rekening {$this->kontrak->nama_bank} Anda dengan No Ref: {$this->pencairan->nomor_referensi}. Selamat melaksanakan penelitian!",
            'url' => route('pengusul.kontrak.index'),
        ];
    }
}

