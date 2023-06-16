<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnnouncementImage;
use App\Models\AnnouncementsStatuses;
use App\Models\User;



class Announcement extends Model
{
    protected $fillable = ['title', 'description', 'price', 'user_id', 'category_id', 'location', 'postal_code', 'phone_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function status()
    {
        return $this->belongsTo(AnnouncementsStatuses::class);
    }

    public function images()
    {
        return $this->hasMany(AnnouncementImage::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'announcement_tags');
    }



}