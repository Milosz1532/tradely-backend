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
        Schema::create('announcement_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('filter_id');
            $table->unsignedBigInteger('filter_value_id')->nullable();
            $table->unsignedBigInteger('custom_value')->nullable();
            $table->timestamps();

            $table->foreign('announcement_id')->references('id')->on('announcements');
            $table->foreign('filter_id')->references('id')->on('subcategories_filters');
            $table->foreign('filter_value_id')->references('id')->on('subcategories_filters_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_filters');
    }
};
