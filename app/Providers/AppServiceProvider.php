<?php

namespace App\Providers;

use App\Events\MemberInvitedEvent;
use App\Listeners\SendMemberInvitationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || str_contains(request()->getHost() ?? '', 'railway.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Event::listen(MemberInvitedEvent::class, SendMemberInvitationNotification::class);
    }
}
