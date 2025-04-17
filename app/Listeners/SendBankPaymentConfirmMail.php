<?php

namespace App\Listeners;

use App\Events\BankPaymentConfirm;
use App\Mail\BankPaymentConfirmMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBankPaymentConfirmMail
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
    public function handle(BankPaymentConfirm $event): void
    {
        //
        $booking = $event->booking;
        $user = $booking->investor;

        Mail::to($user->email)->send(new BankPaymentConfirmMail($booking));
    }
}
