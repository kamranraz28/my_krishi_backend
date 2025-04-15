<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'status',
        'unique_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasOne(Projectdetail::class, 'project_id');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'project_id');
    }
    public function projectupdate()
    {
        return $this->hasMany(Projectupdate::class, 'project_id');
    }

    public function agent()
    {
        return $this->hasMany(Projectagent::class, 'project_id');
    }
    public function cost()
    {
        return $this->hasMany(Projectcost::class, 'project_id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class, 'project_id');
    }

    public static function getProjectList(): Carbon
    {
        return Carbon::create(2025, 4, 17);
    }

}
