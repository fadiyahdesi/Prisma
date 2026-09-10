<?php

namespace App\Notifications;

use App\Models\PpmUsulanAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class MemberInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public PpmUsulanAnggota $member)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->member->loadMissing('usulan.skema');

        return [
            'type' => 'member_invitation',
            'member_id' => $this->member->id,
            'proposal_id' => $this->member->id_usulan,
            'title' => 'Undangan anggota tim menunggu persetujuan',
            'message' => "Anda diundang sebagai {$this->member->peran_anggota} pada {$this->member->usulan->skema->nama_skema}.",
            'url' => route('member-consent.index'),
        ];
    }
}
