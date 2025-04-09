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
        'transaction_id',
        'status',
        'time_to_pay',
        'bank_receipt',
        'payment_method'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    public function projectUpdates()
    {
        return $this->hasMany(Projectupdate::class, 'project_id', 'project_id');
    }

}
