<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;

use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;



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
        ]);
    
        $announcement = Announcement::create($data);

        // Ustawienie updated_at na NULL
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
                return URL::to('/') . Storage::url('announcements/' . $image->image_path);
            })->toArray(),
        ];

        return response()->json($response);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
