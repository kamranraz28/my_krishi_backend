<?php

namespace App\Listeners;

use App\Events\OfficePaymentCancel;
use App\Mail\OfficePaymentCancelMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOfficePaymentCancelMail implements ShouldQueue
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
    public function handle(OfficePaymentCancel $event)
    {
        $booking = $event->booking;
        $user = $booking->investor;

        // Send one email with all booking info
        Mail::to($user->email)->send(new OfficePaymentCancelMail($booking));
    }
}
