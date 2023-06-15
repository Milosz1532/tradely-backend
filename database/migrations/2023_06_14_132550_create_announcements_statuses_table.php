<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AnnouncementsStatuses;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();

        });

        AnnouncementsStatuses::create(['name' => 'Oczekuje na aktywację']);
        AnnouncementsStatuses::create(['name' => 'Aktywne']);
        AnnouncementsStatuses::create(['name' => 'Zawieszone']);
        AnnouncementsStatuses::create(['name' => 'Zakończone']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements_statuses');
    }
};
