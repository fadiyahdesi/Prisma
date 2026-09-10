<?php

namespace App\Listeners;

use App\Events\MemberInvitedEvent;
use App\Notifications\MemberInvitationNotification;

class SendMemberInvitationNotification
{
    public function handle(MemberInvitedEvent $event): void
    {
        $member = $event->member->loadMissing('user', 'usulan.skema');

        if ($member->user) {
            $member->user->notify(new MemberInvitationNotification($member));
        }
    }
}
