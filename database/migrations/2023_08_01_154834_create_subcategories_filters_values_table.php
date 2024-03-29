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
        Schema::create('subcategories_filters_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('filter_id');
            $table->string('value');
            $table->timestamps();

            $table->foreign('filter_id')->references('id')->on('subcategories_filters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories_filters_values');
    }
};
