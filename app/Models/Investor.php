<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'nid',
        'nid_upload',
        'bank_id',
        'acc_name',
        'acc_number',
        'branch_name',
        'routing_number',
        'swift_code',
        'check_upload',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'investor_id', 'id');
    }

}
