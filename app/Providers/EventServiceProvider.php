<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\OtpRequested::class => [
            \App\Listeners\SendOtpViaSms::class,
        ],
        \App\Events\ProjectClosed::class => [
            \App\Listeners\SendProjectClosedEmail::class,
        ],
        \App\Events\OfficePayment::class => [
            \App\Listeners\SendOfficePaymentMail::class,
        ],
        \App\Events\BankPayment::class => [
            \App\Listeners\SendBankPaymentMail::class,
        ],
        \App\Events\OfficePaymentConfirm::class => [
            \App\Listeners\SendOfficePaymentConfirmMail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
