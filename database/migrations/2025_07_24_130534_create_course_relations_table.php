<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_course_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_subject_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('course_subject_id')
                ->references('id')
                ->on('course_subjects')
                ->onDelete('cascade');

            $table->unique(['user_id', 'course_subject_id']);
        });

        Schema::create('type_course_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('course_subject_id');
            $table->timestamps();

            $table->foreign('type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            $table->foreign('course_subject_id')
                ->references('id')
                ->on('course_subjects')
                ->onDelete('cascade');

            $table->unique(['type_id', 'course_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_course_subject');
        Schema::dropIfExists('type_course_subject');
    }
};
