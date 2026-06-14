<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_has_subject_video', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(1)->after('subject_video_id');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_has_subject_video', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
