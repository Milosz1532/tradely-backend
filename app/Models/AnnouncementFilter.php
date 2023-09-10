<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementFilter extends Model
{
    protected $fillable = [
        'announcement_id', 'filter_id', 'filter_value_id','custom_value'
    ];

    public function filter()
    {
        return $this->belongsTo(SubcategoriesFilter::class, 'filter_id');
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}