<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionToSubcategoriesFiltersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subcategories_filters', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('input_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategories_filters', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
}
