<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_hours')->default(0); // إجمالي ساعات الكورس
            $table->integer('total_enrollments')->default(0);
            $table->unsignedBigInteger('course_subject_id');
            $table->unsignedBigInteger('instructor_id'); // مرجع لجدول users
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('course_subject_id')
                ->references('id')
                ->on('course_subjects')
                ->onDelete('cascade');

            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
