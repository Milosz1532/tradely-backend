<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoriesFilter extends Model
{
    use HasFactory;

    public function announcementsWithFilter()
    {
        return $this->hasMany(AnnouncementFilter::class, 'filter_id');
    }

}
