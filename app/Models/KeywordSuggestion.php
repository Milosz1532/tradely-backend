<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeywordSuggestion extends Model
{
    protected $table = 'keyword_suggestions';
    protected $fillable = ['keyword', 'count'];

    public function addOrUpdateSuggestion($keyword)
    {
        $existingSuggestion = self::where('keyword', $keyword)->first();

        if ($existingSuggestion) {
            $existingSuggestion->increment('count');
        } else {
            self::create([
                'keyword' => $keyword,
                'count' => 1,
            ]);
        }
    }

    public function searchKeywordSuggestions($query)
    {
        return self::where('keyword', 'LIKE', "$query%") 
            ->orderByDesc('count')
            ->orderBy('keyword')
            ->limit(10)
            ->pluck('keyword');
    }
    
}
