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
        Schema::create('subjects_video', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
            $table->string('link');
            $table->boolean('is_active')->default(true);
            $table->string('description');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('type_has_subject_video', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('subject_video_id');

            $table->primary(['type_id', 'subject_video_id']);

            $table->foreign('type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            $table->foreign('subject_video_id')
                ->references('id')
                ->on('subjects_video')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_has_subject_video');
        Schema::dropIfExists('subjects_video');
    }
};
