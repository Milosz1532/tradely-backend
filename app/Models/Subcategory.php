<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SubcategoryFilter;


class Subcategory extends Model
{

    public function filters()
    {
        return $this->hasMany(SubcategoryFilter::class);
    }
}
