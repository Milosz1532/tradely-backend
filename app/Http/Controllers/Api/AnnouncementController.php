<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\AnnouncementResource;

use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;

use App\Models\Announcement;
use App\Models\Category;

class AnnouncementController extends Controller
{


    public function index(Request $request)
    {
        $location = $request->input('location');
        $latestAnnouncements = Announcement::query()
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();
        
        $locationAnnouncements = [];
        if ($location) {
            $locationAnnouncements = Announcement::query()
                ->where('location', '=', $location)
                ->where('status_id', '=', 2)
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();
        }
        
        $categoryAnnouncements = Announcement::query()
            ->where('category_id', '=', 1)
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();
        
        return [
            'latest_announcements' => AnnouncementResource::collection($latestAnnouncements),
            'location_announcements' => AnnouncementResource::collection($locationAnnouncements),
            'category_announcements' => AnnouncementResource::collection($categoryAnnouncements),
        ];
        
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
            'location' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp'
        ]);

        $announcement = Announcement::create($data);

        // Przetwarzanie przesłanych zdjęć
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $uuid = Uuid::uuid4()->toString();

                // Konwersja obrazu na format .webp
                $converted = Image::make($image)->encode('webp', 75);

    
                $path = 'public/announcements/' . $announcement->id . '_' . $uuid . '.webp';

    
                Storage::put($path, $converted->stream());
    
                $announcement->images()->create([
                    'image_path' => $path
                ]);
            }
        }
        

        $announcement->updated_at = null;
        $announcement->save();

        return response()->json($announcement, 201);
    }

    public function show(string $id)
    {
        $announcement = Announcement::with('images')->findOrFail($id);
        $user = $announcement->User;

        $totalAnnouncements = $user->announcements()->count();

        $response = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'description' => $announcement->description,
            'price' => $announcement->price,
            'status' => $announcement->status,
            'location' => [
                'location_name' => $announcement->location,
                'postal_code' =>$announcement->postal_code,
            ],
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'created_at' => $user->created_at ? $user->created_at->format('d.m.Y H:i:s') : trans('messages.no_data'),
                'total_announcements' => $totalAnnouncements,
            ],
            'created_at' => optional($announcement->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => optional($announcement->updated_at)->format('d.m.Y H:i:s'),
            'images' => $announcement->images->map(function ($image) {
                return URL::to('/') . Storage::url($image->image_path);
            })->toArray(),
        ];

        return response()->json($response);
    }



    public function search(Request $request)
{
    $location = $request->input('location') ?? "all_locations";
    $categoryName = $request->input('category') ?? "all_categories";
    $keyword = $request->input('keyword');

    $query = Announcement::query();

    if ($location !== 'all_locations') {
        $query->where('location', $location);
    }

    if ($categoryName !== 'all_categories') {
        $category = Category::where('name', $categoryName)->first();

        $query->where('category_id', $category->id);
    }

    if (!empty($keyword)) {
        $query->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }

    $announcements = $query->orderBy('id', 'desc')->paginate(5);

    return AnnouncementResource::collection($announcements);
}





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $announcement = Announcement::findOrFail($id);

        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'user_id' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048' // Walidacja zdjęć (opcjonalna)
        ]);

        $announcement->update($data);

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('announcements');
                $images[] = new AnnouncementImage(['image_path' => $imagePath]);
            }
            $announcement->images()->delete();
            $announcement->images()->saveMany($images);
        }

        $announcement->refresh();

        $response = new AnnouncementResource($announcement);
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->images()->delete();

        $announcement->delete();

        return response()->json(null, 204);
    }
}
