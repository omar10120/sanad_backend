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

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('previous_question_id')->nullable();
            $table->unsignedBigInteger('next_question_id')->nullable();
            $table->json('text_question');
            $table->string('question_photo')->nullable();
            $table->json('choices');
            $table->tinyInteger('right_choice')->nullable();
            $table->boolean('is_edited')->default(0);
            $table->json('hint')->nullable();
            $table->string('hint_photo')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->foreign('type_id')
                ->references('id')
                ->on('question_types')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('previous_question_id')
                ->references('id')
                ->on('questions')
                ->nullOnDelete();

            $table->foreign('next_question_id')
                ->references('id')
                ->on('questions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
