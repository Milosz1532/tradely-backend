<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;



class AnnouncementResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $firstImage = $this->images->first();
        $imageUrl = $firstImage ? URL::to('/') . Storage::url('announcements/' . $firstImage->image_path) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'first_image' => $imageUrl,
        ];
    }
}
