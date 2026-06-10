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
        // Drop course-related tables in reverse dependency order
        Schema::dropIfExists('video_processing_logs');
        Schema::dropIfExists('video_quality_settings');
        Schema::dropIfExists('type_course_subject');
        Schema::dropIfExists('user_course_subject');
        Schema::dropIfExists('video_streaming_tokens');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_codes');
        Schema::dropIfExists('course_code_packages');
        Schema::dropIfExists('video_qualities');
        Schema::dropIfExists('course_videos');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_units');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_subjects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration cannot be reversed as we're dropping tables
        // If you need to restore this functionality, you would need to recreate
        // the tables from their original migration files
        throw new Exception('This migration cannot be reversed. Course functionality has been permanently removed.');
    }
};