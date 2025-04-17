<?php

namespace App\Listeners;

use App\Events\ProjectClosed;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectClosedMail;

class SendProjectClosedEmail
{
    public function handle(ProjectClosed $event)
    {
        $project = $event->project;

        $bookings = Booking::with('investor','project')
            ->where('project_id', $project->id)
            ->get();

        foreach ($bookings as $booking) {
            if ($booking->investor && $booking->investor->email) {
                Mail::to($booking->investor->email)
                ->send(new ProjectClosedMail($project, $booking));
            }
        }
    }
}
