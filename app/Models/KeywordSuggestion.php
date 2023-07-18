<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeywordSuggestion extends Model
{
    protected $table = 'keyword_suggestions';

    protected $searchable = [
        'keyword',
    ];

    public function searchKeywordSuggestions($query)
    {
        return self::where('keyword', 'LIKE', "%$query%")
            ->limit(10)
            ->pluck('keyword');
        }
    
}
