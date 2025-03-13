<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'project_id',
        'unit',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }
    public function investor()
    {
        return $this->belongsTo(User::class,'investor_id');
    }
}
