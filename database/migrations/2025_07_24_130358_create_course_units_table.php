<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_free')->default(false);
            $table->unsignedBigInteger('course_id');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            $table->index(['course_id', 'order']);
            $table->index(['course_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_units');
    }
};
