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
        Schema::table('teachers', function (Blueprint $table) {
            // Drop the column if it exists
            $table->dropColumn('estimation_time');
        });

        Schema::table('teachers', function (Blueprint $table) {
            // Re-add it as TIME (nullable)
            $table->time('estimation_time')->nullable();
        });

        Schema::table('youtube_links_video', function (Blueprint $table) {
            // Drop the column if it exists
            $table->dropColumn('video_time');
        });

        Schema::table('youtube_links_video', function (Blueprint $table) {
            // Re-add it as TIME (nullable)
            $table->time('video_time')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('estimation_time');
            $table->integer('estimation_time')->nullable(); // adjust to your previous type
        });
        Schema::table('youtube_links_video', function (Blueprint $table) {
            $table->dropColumn('video_time');
            $table->integer('video_time')->nullable(); // adjust to your previous type
        });
    }
};
