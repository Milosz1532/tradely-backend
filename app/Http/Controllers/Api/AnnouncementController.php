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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AnnouncementResource::collection( 
            Announcement::query()->orderBy('id','desc')->paginate(20)
        ); 
    
    }

    /**
     * Store a newly created resource in storage.
     */
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $announcement = Announcement::with('images')->findOrFail($id);

        $response = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'description' => $announcement->description,
            'price' => $announcement->price,
            'user_id' => $announcement->user_id,
            'created_at' => $announcement->created_at,
            'updated_at' => $announcement->updated_at,
            'images' => $announcement->images->map(function ($image) {
                return URL::to('/') . Storage::url($image->image_path);
            })->toArray(),
        ];

        return response()->json($response);
    }

    // public function search(Request $request)
    // {
    //     $location = $request->input('location');
    //     $category = $request->input('category');
    //     $keyword = $request->input('keyword');

    //     $query = Announcement::query();

    //     if ($location !== 'default') {
    //         $query->where('location', $location);
    //     }

    //     if ($category !== 'default') {
    //         $query->where('category_id', $category);
    //     }

    //     if (!empty($keyword)) {
    //         $query->where(function ($query) use ($keyword) {
    //             $query->where('title', 'like', "%$keyword%")
    //                 ->orWhere('description', 'like', "%$keyword%");
    //         });
    //     }

    //     $announcements = $query->orderBy('id', 'desc')->paginate(20);

    //     return AnnouncementResource::collection($announcements);
    // }
    public function search(Request $request)
{
    $location = $request->input('location') ?? "default";
    $categoryName = $request->input('category') ?? "default";
    $keyword = $request->input('keyword');

    $query = Announcement::query();

    if ($location !== 'default') {
        $query->where('location', $location);
    }

    if ($categoryName !== 'default') {
        $category = Category::where('name', $categoryName)->first();

        $query->where('category_id', $category->id);
    }

    if (!empty($keyword)) {
        $query->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }

    $announcements = $query->orderBy('id', 'desc')->paginate(20);

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
