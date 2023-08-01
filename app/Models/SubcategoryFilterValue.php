<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SubcategoryFilter;


class SubcategoryFilterValue extends Model
{
    use HasFactory;

    protected $table = 'subcategories_filters_values';

    public function filter()
    {
        return $this->belongsTo(SubcategoryFilter::class, 'filter_id');
    }

}
