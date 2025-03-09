<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectupdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'update_by',
        'description',
        'image'
    ];

    protected $casts = [
        'image' => 'array', // Now Laravel will handle the JSON column as an array
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'projectupdate_id');
    }

}
