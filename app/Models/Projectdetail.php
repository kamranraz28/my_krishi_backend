<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;

class Projectdetail extends Model
{
    use HasFactory;
    use HasRichText;


    protected $fillable = [
        'project_id',
        'title',
        'total_price',
        'unit_price',
        'unit',
        'location',
        'location_map',
        'description',
        'image',
        'duration',
        'return_amount',
        'closing_amount',
        'service_charge',
        'booked_unit',
        'remarks',
        'voucher_file',
        'youtube_video',
        'maturity_date',
        'terms_link',
    ];

    protected $appends = ['image_url'];
    protected $richTextAttributes = [
        'description', // this enables rich text storage for `description`
    ];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function getImageUrlAttribute()
    {
        return url('uploads/projects/' . $this->image);
    }

}
