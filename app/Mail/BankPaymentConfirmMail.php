<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankPaymentConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        //
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->view('emails.bank_payment_confirm')
                    ->subject('Bank Payment Confirmation')
                    ->with([
                        'bookings' => $this->booking,
                    ]);
    }
}
