<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectdetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'total_price',
        'unit_price',
        'unit',
        'location',
        'description',
        'image',
        'duration',
        'return_amount',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
