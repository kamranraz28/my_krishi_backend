<?php

namespace App\Listeners;

use App\Events\BankPaymentCancel;
use App\Mail\BankPaymentCancelMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBankPaymentCancelMail
{
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
    public function handle(BankPaymentCancel $event): void
    {
        //
        $booking = $event->booking;
        $user = $booking->investor;

        Mail::to($user->email)->send(new BankPaymentCancelMail($booking));
    }
}
