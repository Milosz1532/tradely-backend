<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SubcategoryFilterValue;


class SubcategoryFilter extends Model
{

    protected $table = 'subcategories_filters';

    public function values()
    {
        return $this->hasMany(SubcategoryFilterValue::class, 'filter_id');
    }
}
