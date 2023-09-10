<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subcategories_filters', function (Blueprint $table) {
            $table->enum('context', ['search', 'create', 'all']);
            $table->unsignedBigInteger('belong_to')->nullable();
            $table->foreign('belong_to')->references('id')->on('subcategories_filters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategories_filters', function (Blueprint $table) {
            $table->dropForeign(['belong_to']);
            $table->dropColumn('belong_to');
            $table->dropColumn('context');
        });
    }
};
