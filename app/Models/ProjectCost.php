<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectcost extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'field',
        'cost',
        'memo',
        'voucher',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
