<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_qualities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_video_id');
            $table->enum('quality', ['144p', '240p', '360p', '480p', '720p', '1080p', '1440p', '2160p']);
            $table->string('video_url');
            $table->string('video_format')->default('mp4');
            $table->integer('file_size');
            $table->integer('bitrate')->nullable();
            $table->integer('width');
            $table->integer('height');
            $table->decimal('fps', 5, 2)->default(30.00);
            $table->string('codec')->default('h264');
            $table->boolean('is_available')->default(true);
            $table->json('encryption_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('course_video_id')
                ->references('id')
                ->on('course_videos')
                ->onDelete('cascade');

            $table->index(['course_video_id', 'quality']);
            $table->index(['course_video_id', 'is_available']);
            $table->unique(['course_video_id', 'quality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_qualities');
    }
};
