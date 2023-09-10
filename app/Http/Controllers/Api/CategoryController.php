<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all(); 
        $subcategories = Subcategory::all(); 

        $categoriesData = [];
        foreach ($categories as $category) {
            $categoryData = [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                // 'image_path' => $category->image_path ? URL::to('/') . Storage::url($category->image_path) : null,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'subcategories' => []
            ];

            foreach ($subcategories as $subcategory) {
                if ($subcategory->category_id === $category->id) {
                    $categoryData['subcategories'][] = [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'created_at' => $subcategory->created_at,
                        'updated_at' => $subcategory->updated_at,
                    ];
                }
            }

            $categoriesData[] = $categoryData;
        }

        return response()->json($categoriesData);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
