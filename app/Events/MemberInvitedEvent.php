<?php

namespace App\Events;

use App\Models\PpmUsulanAnggota;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberInvitedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public PpmUsulanAnggota $member)
    {
    }
}
