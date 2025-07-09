<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankdetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'bank_id',
        'acc_name',
        'acc_number',
        'branch_name',
        'routing_number',
        'swift_code',
        'cheque_upload',
    ];

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
