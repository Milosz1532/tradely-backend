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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;


use App\Models\Announcement;
use App\Models\AnnouncementFilter;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Models\KeywordSuggestion;
use App\Models\SubcategoriesFilter;
use App\Models\SubcategoryFilterValue;


class AnnouncementController extends Controller
{


    public function index(Request $request)
    {

        $recentAnnouncementIds = $request->input('recentAnnouncementIds');
        $recentAnnouncementIds = explode(',', $recentAnnouncementIds);

        $userLatitude = $request->input('userLatitude');
        $userLongitude = $request->input('userLongitude');

        $latestAnnouncements = Announcement::query()
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();



        $categoryAnnouncements = Announcement::query()
            ->where('category_id', '=', 1)
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        $recentAnnouncements = [];
        if ($recentAnnouncementIds) {
            $orderByIds = implode(',', $recentAnnouncementIds);

            $recentAnnouncements = Announcement::whereIn('id', $recentAnnouncementIds)
                ->orderByRaw("FIELD(id, $orderByIds)")
                ->get();
        }

        if ($userLatitude && $userLongitude) {
            $nearbyAnnouncements = Announcement::selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$userLatitude, $userLongitude, $userLatitude])
                ->where('status_id', '=', 2)
                ->having('distance', '<=', 15) // Odległość 10 km
                ->orderBy('distance')
                ->take(20)
                ->get();
        } else {
            $nearbyAnnouncements = [];
        }

        return [
            'latest_announcements' => AnnouncementResource::collection($latestAnnouncements),
            'category_announcements' => AnnouncementResource::collection($categoryAnnouncements),
            'recent_announcements' => AnnouncementResource::collection($recentAnnouncements),
            'nearby_announcements' => AnnouncementResource::collection($nearbyAnnouncements),
        ];
    }



    public function userFavoriteAnnouncements(Request $request) 
    {
        return AnnouncementResource::collection($request->user()->favoriteAnnouncements);
    }

    public function userAnnouncements(Request $request) 
    {
        $userId = $request->user()->id;
        $active_announcements = Announcement::query()
        ->where('user_id', $userId)
        ->where('status_id', 2)
        ->orderBy('id', 'desc')
        ->take(20)
        ->get();

        $pending_announcements = Announcement::query()
        ->where('user_id', $userId)
        ->where('status_id', 1)
        ->orderBy('id', 'desc')
        ->take(20)
        ->get();

        $completed_announcements = Announcement::query()
        ->where('user_id', $userId)
        ->where('status_id', 4)
        ->orderBy('id', 'desc')
        ->take(20)
        ->get();
    
    

        return [
            'active_announcements' => AnnouncementResource::collection($active_announcements),
            'pending_announcements' => AnnouncementResource::collection($pending_announcements),
            'completed_announcements' => AnnouncementResource::collection($completed_announcements),
        ];
    }

    public function getUserActiveAnnouncements(Request $request) 
    {
        $userId = $request->user()->id;
        
        $query = Announcement::query()
            ->where('user_id', $userId)
            ->where('status_id', 2)
            ->orderBy('id', 'desc');
    
        $perPage = 5; 
        $active_announcements = $query->paginate($perPage);
    
        return AnnouncementResource::collection($active_announcements);
    }


    public function getUserCompletedAnnouncements(Request $request) 
    {
        $userId = $request->user()->id;
        
        $query = Announcement::query()
            ->where('user_id', $userId)
            ->where('status_id', 4)
            ->orderBy('id', 'desc');
    
        $perPage = 5; 
        $active_announcements = $query->paginate($perPage);
    
        return AnnouncementResource::collection($active_announcements);
    }
    

    public function store(Request $request)
    {
        // dd($request->input('filters'));
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'province' => 'required',
            'location' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone_number' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4048',
            'tags' => 'array', 
        ]);

        if ($validator->fails()) {
            // return response()->json(['message' => $validator->errors()], 400);
            return response()->json(['message' => "Wprowadzone dane nie są prawidłowe. Upewnij się, czy wszystkie pola są uzupełnione."], 400);
           

        }

        $user = $request->user();
        $userId = $user->id;

        if (!$userId) {
            return response()->json(['message' => 'Tylko zalogowani użytkownicy mogą dodawać ogłoszenia.'], 400);
        }


        $tags = $request->input('tags', []);


        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        $announcement = Announcement::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price_type') == 1 && $request->input('price') > 0 ? $request->input('price') : null,
            'user_id' => $userId, 
            'category_id' => $request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id'),
            'location' => $request->input('location'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'phone_number' => $request->input('phone_number'),
            'province' => $request->input('province'),
            'price_type' => $request->input('price_type')
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uuid = Uuid::uuid4()->toString();
                $converted = Image::make($image)->encode('webp', 75);

                $path = 'public/announcements/' . $announcement->id . '_' . $uuid . '.webp';
                Storage::put($path, $converted->stream());

                $announcement->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        $filters = $request->input('filters');

        if (!empty($filters)) {
            $filtersArray = [];

            $filtersArray = json_decode($filters, true);

            if ($filtersArray === null && json_last_error() !== JSON_ERROR_NONE) {
                return;
            }

            foreach ($filtersArray as $filterId => $value) {
                $filter = SubcategoriesFilter::find($filterId);
        
                if ($filter) {
                    $filterValueId = null;
                    $customValue = null;
        
                    if ($filter->input_type !== 'input') {
                        $filterValueId = intval($value);
                    } else {
                        $customValue = $value;
                    }
        
                    AnnouncementFilter::create([
                        'announcement_id' => $announcement->id,
                        'filter_id' => $filter->id,
                        'filter_value_id' => $filterValueId,
                        'custom_value' => $customValue,
                    ]);
                }
            }
        }

        $announcement->tags()->attach($tagIds);

        return response()->json($announcement, 201);
    }
    


    public function show(string $id)
    {
        $announcement = Announcement::with('images')->findOrFail($id);
        $user = $announcement->User;

        $totalAnnouncements = $user->announcements()->count();

        $filters = $announcement->filters->map(function ($filter) {
            $filterData = [
                'id' => $filter->id,
                'filter_id' => $filter->filter_id,
                'filter_value_id' => $filter->filter_value_id,
                'custom_value' => $filter->custom_value,
            ];
        
            if ($filter->filter) {
                $filterData['name'] = $filter->filter->name;
            }
        
            // Pobierz nazwę wartości filtra na podstawie filter_value_id
            if ($filter->filter_value_id) {
                $filterValue = SubcategoryFilterValue::find($filter->filter_value_id);
                if ($filterValue) {
                    $filterData['filter_value'] = $filterValue->value;
                }
            }
        
            return $filterData;
        })->values();
        

        $response = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'description' => $announcement->description,
            'category' => $announcement->category,
            'subcategory' => $announcement->subcategory,
            'price' => $announcement->price,
            'price_type' => $announcement->price_type,
            'status' => [
                'id' => $announcement->status->id,
                'name' => $announcement->status->name,
            ],
            'tags' => $announcement->tags->map(function ($tag) {
                return ['id' => $tag->id, 'name' => $tag->name];
            })->values(),
            'location' => [
                'location_name' => $announcement->location,
                'latitude' =>$announcement->latitude,
                'longitude' => $announcement->longitude,
            ],
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'created_at' => $user->created_at ? $user->created_at->format('d.m.Y H:i:s') : trans('messages.no_data'),
                'total_announcements' => $totalAnnouncements,
            ],
            'phone_number' => $announcement->phone_number,
            'created_at' => optional($announcement->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => optional($announcement->updated_at)->format('d.m.Y H:i:s'),
            'images' => $announcement->images->map(function ($image) {
                return URL::to('/') . Storage::url($image->image_path);
            })->toArray(),
            'favorite_count' => $announcement->favoritedByUsers->count(),
            'product_state' => $announcement->product_state,
        ];

        $response['filters'] = $filters;


        return response()->json($response);
    }



    public function search(Request $request)
    {
        $location = $request->input('location') ?? "all_locations";
        $categoryName = $request->input('category') ?? "all_categories";
        $subcategoryName = $request->input('subcategory') ?? "all_subcategories";
        $keyword = $request->input('keyword');
        $filters = $request->input('filters');
        // $distance = $request->input('distance');
        $amountFrom = $request->input('amountFrom');
        $amountTo = $request->input('amountTo');
        $sortType = $request->input('sortType');



        $filtersArray = json_decode($filters, true);

        $query = Announcement::query()->where('status_id', 2);

        if ($location !== 'all_locations') {
            $query->where('location', $location);
        }

        if ($categoryName !== 'all_categories') {
            $category = Category::where('name', $categoryName)->first();
            $query->where('category_id', $category->id);
    
            if ($subcategoryName !== 'all_subcategories') {
                $subcategory = Subcategory::where('name', $subcategoryName)
                    ->where('category_id', $category->id)
                    ->first();
    
                if ($subcategory) {
                    $query->where('subcategory_id', $subcategory->id);
                }
            }
        }

        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%");
            });
            $keywordSuggestion = new KeywordSuggestion();
            $keywordSuggestion->addOrUpdateSuggestion($keyword);
        }

        if ($amountFrom) {
            $query->where('price', '>=', $amountFrom);
        }


        if ($amountTo) {
            $query->where('price', '<=', $amountTo);
        }


        if (!empty($filtersArray)) {
            $query->where(function ($mainQuery) use ($filtersArray) {
                foreach ($filtersArray['dynamicFilters'] as $filterId => $value) {
                    $filter = SubcategoriesFilter::find($filterId);
                    if ($filter) {
                        $condition = $filter->condition ?? '=';
                        $subQuery = AnnouncementFilter::query()
                            ->whereColumn('announcements.id', 'announcement_filters.announcement_id')
                            ->where('announcement_filters.filter_id', $filterId)
                            ->where(function ($subquery) use ($filter, $value, $condition) {
                                if ($filter->input_type !== 'input') {
                                    $subquery->where('announcement_filters.filter_value_id', intval($value));
                                } else {
                                    $subquery->where('announcement_filters.custom_value', $condition, intval($value));
                                }
                            });
                        $mainQuery->whereExists($subQuery);
                    }
                }
            });
        }

        if (!empty($filtersArray['offerProductState'])) {
            $query->whereIn('product_state', $filtersArray['offerProductState']);
        }

        if (!empty($filtersArray['offerPricesTypes'])) {
            $query->whereIn('price_type', $filtersArray['offerPricesTypes']);
        }
        
        

        switch ($sortType) {
            case 1:
                $query->orderBy('created_at', 'desc'); // Sort by newest
                break;
            case 2:
                $query->orderBy('created_at', 'asc'); // Sort by oldest
                break;
            case 3:
                $query->orderBy('price', 'desc'); // Sort by price descending
                break;
            case 4:
                $query->orderBy('price', 'asc'); // Sort by price ascending
                break;
            default:
                $query->orderBy('id', 'desc'); // Default sorting
        }
        


        $announcements = $query->orderBy('id', 'desc')->paginate(5);

        return AnnouncementResource::collection($announcements);
    }




    public function likeAnnouncement(Request $request)
    {
        $id = $request->input('id');
        $user = $request->user();
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['message' => 'Ogłoszenie nie istnieje.'], 404);
        }

        if ($announcement->favoritedByUsers()->where('user_id', $user->id)->exists()) {
            $announcement->favoritedByUsers()->detach($user->id);
            return response()->json(['success' => true, 'status' => 0]);
        }

        $announcement->favoritedByUsers()->attach($user->id);

        return response()->json(['success' => true, 'status' => 1]);
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
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
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
