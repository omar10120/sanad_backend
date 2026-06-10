<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_streaming_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_video_id');
            $table->string('requested_quality')->nullable();
            $table->unsignedBigInteger('video_quality_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('video_quality_id')
                ->references('id')
                ->on('video_qualities')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('course_video_id')
                ->references('id')
                ->on('course_videos')
                ->onDelete('cascade');

            $table->index(['video_quality_id', 'expires_at']);
            $table->index(['token', 'expires_at']);
            $table->index(['student_id', 'course_video_id']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_streaming_tokens');
    }
};
