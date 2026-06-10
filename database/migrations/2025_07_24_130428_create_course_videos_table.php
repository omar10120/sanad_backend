<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('video_id')->unique();
            $table->string('thumbnail')->nullable();
            $table->integer('duration_seconds');
            $table->string('original_quality')->default('1080p');
            $table->integer('total_file_size')->nullable();
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending');
            $table->json('processing_progress')->nullable();
            $table->string('video_format')->default('mp4');
            $table->json('encryption_data')->nullable();
            $table->boolean('is_encrypted')->default(true);
            $table->string('stream_key')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->unsignedBigInteger('course_lesson_id');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('course_lesson_id')
                ->references('id')
                ->on('course_lessons')
                ->onDelete('cascade');

            $table->index(['course_lesson_id', 'order']);
            $table->index(['course_lesson_id', 'is_active']);
            $table->index('video_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_videos');
    }
};
