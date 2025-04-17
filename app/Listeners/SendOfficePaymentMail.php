<?php

namespace App\Listeners;

use App\Events\OfficePayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfficePaymentMail;

class SendOfficePaymentMail implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OfficePayment $event)
    {
        $bookings = $event->bookings;
        $user = $bookings->first()->investor;

        // Send one email with all booking info
        Mail::to($user->email)->send(new OfficePaymentMail($bookings));
    }
}
