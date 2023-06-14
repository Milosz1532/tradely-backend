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
        $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'category' => $this->category->name,
            'description' => $this->description,
            'location' => $this->location,
            'price' => $this->price,
            'user_id' => $this->user_id,
            'created_at' => optional($this->created_at)->format('d.m.Y H:i:s'),

            'updated_at' => optional($this->updated_at)->format('d.m.Y H:i:s'),

            'first_image' => $imageUrl,
        ];
    }
}
