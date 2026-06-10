<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_processing_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_video_id');
            $table->string('quality');
            $table->enum('status', ['started', 'completed', 'failed']);
            $table->json('processing_details')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('processing_time')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('course_video_id')
                ->references('id')
                ->on('course_videos')
                ->onDelete('cascade');

            $table->index(['course_video_id', 'status']);
            $table->index(['quality', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_processing_logs');
    }
};
