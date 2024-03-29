<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class UserAnnouncementsStats extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstImage = $this->images->first();
        $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;
        $user = $request->user('api');

        $isFavorited = $user ? $this->favoritedByUsers()
        ->where('user_id', $user->id)
        ->exists() : false;


        return [
            'id' => $this->id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'category' => $this->category->name,
            'tags' => $this->tags->map(function ($tag) {
                return ['id' => $tag->id, 'name' => $tag->name];
            })->values(),
            'price' => $this->price,
            'created_at' => optional($this->created_at)->format('d.m.Y H:i:s'),

            'updated_at' => optional($this->updated_at)->format('d.m.Y H:i:s'),

            'first_image' => $imageUrl,
            'favorite_count' => $this->favoritedByUsers->count(),
        ];
    }
}
