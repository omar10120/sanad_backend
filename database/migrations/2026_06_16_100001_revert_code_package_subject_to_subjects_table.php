<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects_video')
                ->onDelete('cascade');
        });
    }
};
