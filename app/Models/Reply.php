<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'replies';

    protected $fillable = [
        'comment_id',
        'replied_by',
        'reply'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
