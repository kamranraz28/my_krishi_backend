<?php

namespace App\Listeners;

use App\Events\OnlinePayment;
use App\Mail\OnlinePaymentMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class sendOnlinePaymentMail implements ShouldQueue
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
    public function handle(OnlinePayment $event)
    {
        \Log::info('Listener triggered for OnlinePayment event.');
        $bookings = $event->bookings;
        $user = $bookings->first()->investor;

        // Send one email with all booking info
        Mail::to($user->email)->send(new OnlinePaymentMail($bookings));
    }
}
