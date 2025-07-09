<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OnlinePaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $bookings;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $bookings)
    {
        $this->bookings = $bookings;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.online_payment')
                    ->subject('Local-Online Payment Booking Confirmation')
                    ->with([
                        'bookings' => $this->bookings,
                    ]);
    }
}
