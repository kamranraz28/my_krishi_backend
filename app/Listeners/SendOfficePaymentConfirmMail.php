<?php

namespace App\Listeners;

use App\Events\OfficePaymentConfirm;
use App\Mail\OfficePaymentConfirmMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfficePaymentMail;

class SendOfficePaymentConfirmMail implements ShouldQueue
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
    public function handle(OfficePaymentConfirm $event)
    {
        $booking = $event->booking;
        $user = $booking->investor;

        // Send one email with all booking info
        Mail::to($user->email)->send(new OfficePaymentConfirmMail($booking));
    }
}
