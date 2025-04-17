<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectClosed
{
    use Dispatchable, SerializesModels;

    public $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}
