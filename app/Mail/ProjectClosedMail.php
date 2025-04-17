<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectClosedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $booking;

    public function __construct(Project $project, Booking $booking)
    {
        $this->project = $project;
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Project Closed - My Krishi')
                    ->view('emails.project_closed')
                    ->with([
                        'project' => $this->project,
                        'booking' => $this->booking,
                    ]);
    }
}
