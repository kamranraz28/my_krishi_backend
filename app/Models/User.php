<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'level',
        'unique_id',
        'address',
        'image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function project()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'investor_id');
    }
    public function projectupdate()
    {
        return $this->hasMany(Projectupdate::class, 'update_by');
    }
    public function agent()
    {
        return $this->hasMany(Projectagent::class, 'agent_id');
    }
    public function comment()
    {
        return $this->hasMany(Comment::class, 'comment_by');
    }
    public function reply()
    {
        return $this->hasMany(Reply::class, 'comment_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'investor_id');
    }

    public function investor()
    {
        return $this->hasOne(Investor::class, 'investor_id');
    }

    public function getImageUrlAttribute()
    {
        return url('uploads/investors/' . $this->image);
    }
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot('is_seen')
                    ->withTimestamps();
    }

}
