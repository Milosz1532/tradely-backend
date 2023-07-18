<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\KeywordSuggestion;

class KeywordSuggestionController extends Controller
{
    public function getSuggestions(Request $request)
    {
        $query = $request->input('keyword');

        if (empty($query)) {
            return response()->json([]);
        }


        $suggestions = (new KeywordSuggestion)->searchKeywordSuggestions($query);

        return response()->json($suggestions);
    }
}