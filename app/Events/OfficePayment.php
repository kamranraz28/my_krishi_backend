<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OfficePayment
{
    use Dispatchable, SerializesModels;

    public Collection $bookings;

    public function __construct(Collection $bookings)
    {
        $this->bookings = $bookings;
    }
}
