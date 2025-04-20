<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'created_by', 'message'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('is_seen')
                    ->withTimestamps();
    }

}
