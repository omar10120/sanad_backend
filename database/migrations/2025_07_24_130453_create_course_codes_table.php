<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            $table->foreign('package_id')
                ->references('id')
                ->on('course_code_packages')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('set null');

            $table->index(['course_id', 'is_active']);
            $table->index(['package_id', 'is_active']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_codes');
    }
};
