<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountToKeywordSuggestionsTable extends Migration
{

    public function up()
    {
        Schema::table('keyword_suggestions', function (Blueprint $table) {
            $table->integer('count')->default(0); 
        });
    }


    public function down()
    {
        Schema::table('keyword_suggestions', function (Blueprint $table) {
            $table->dropColumn('count'); 
        });
    }
}
