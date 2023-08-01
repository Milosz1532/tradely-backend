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
        $categoryData = $category->toArray();
        
        $categoryData['image_path'] = $category->image_path ? URL::to('/') . Storage::url($category->image_path) : null;


        $categoriesData[] = $categoryData;
    }


    return response()->json([
        'categories' => $categoriesData,
        'subcategories' => $subcategories,
    ]);
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
