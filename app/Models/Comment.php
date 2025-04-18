<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'projectupdate_id',
        'comment_by',
        'comment',
    ];

    public function projectUpdate()
    {
        return $this->belongsTo(Projectupdate::class, 'projectupdate_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'comment_by')
            ->select('id', 'name', 'level', 'unique_id');
    }
    public function reply()
    {
        return $this->hasMany(Reply::class, 'comment_id');
    }
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
