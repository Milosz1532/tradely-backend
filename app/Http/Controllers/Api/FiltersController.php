<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subcategory;

class FiltersController extends Controller
{
    public function getFiltersForSubcategory($subcategoryId, $context)
    {
        $subcategory = Subcategory::with(['filters.values', 'filters.subcategories'])
            ->findOrFail($subcategoryId);

        $filters = $subcategory->filters->filter(function ($filter) use ($context) {
            return $context === 'all' || $filter->context === 'all' || $filter->context === $context;
        })->map(function ($filter) {
            return [
                'id' => $filter->id,
                'name' => $filter->name,
                'placeholder' => $filter->placeholder,
                'input_type' => $filter->input_type,
                'values' => $filter->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                    ];
                })->values(), 
            ];
        })->values(); 

        return response()->json(['filters' => $filters->toArray()]);
    }
}


