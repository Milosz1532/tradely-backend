<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeywordSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyword_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('keyword'); // Kolumna zawierająca słowa kluczowe
            $table->timestamps();
        });

        // Dodaj indeks pełnotekstowy dla kolumny 'keyword'
        DB::statement('ALTER TABLE keyword_suggestions ADD FULLTEXT INDEX keyword_fulltext_index (keyword)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Usuń indeks pełnotekstowy przed usunięciem tabeli
        DB::statement('ALTER TABLE keyword_suggestions DROP INDEX keyword_fulltext_index');

        Schema::dropIfExists('keyword_suggestions');
    }
}
