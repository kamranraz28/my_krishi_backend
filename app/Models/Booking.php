<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'investor_id',
        'total_unit',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }
}
