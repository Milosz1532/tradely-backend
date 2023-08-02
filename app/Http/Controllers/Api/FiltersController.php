<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subcategory;

class FiltersController extends Controller
{
    public function getFiltersForSubcategory($subcategoryId)
    {
        $subcategory = Subcategory::with(['filters.values', 'filters.subcategories'])->findOrFail($subcategoryId);

        $filters = $subcategory->filters->map(function ($filter) {
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
                }),
            ];
        });

        return response()->json(['filters' => $filters]);
    }
}
