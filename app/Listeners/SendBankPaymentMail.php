<?php

namespace App\Listeners;

use App\Events\BankPayment;
use App\Mail\BankPaymentMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBankPaymentMail implements ShouldQueue
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
    public function handle(BankPayment $event)
    {
        $bookings = $event->bookings;
        $user = $bookings->first()->investor;

        // Send one email with all booking info
        Mail::to($user->email)->send(new BankPaymentMail($bookings));
    }
}
