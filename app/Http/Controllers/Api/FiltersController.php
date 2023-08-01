<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subcategory;

class FiltersController extends Controller
{
    public function getFiltersForSubcategory($subcategoryId)
    {
        $subcategory = Subcategory::with('filters.values')->findOrFail($subcategoryId);

        $filters = $subcategory->filters->map(function ($filter) {
            return [
                'id' => $filter->id,
                'subcategory_id' => $filter->subcategory_id,
                'name' => $filter->name,
                'placeholder' => $filter->placeholder,
                'input_type' => $filter->input_type,
                'values' => $filter->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'filter_id' => $value->filter_id,
                        'value' => $value->value,
                    ];
                }),
            ];
        });

        return response()->json(['filters' => $filters]);
    }
}
