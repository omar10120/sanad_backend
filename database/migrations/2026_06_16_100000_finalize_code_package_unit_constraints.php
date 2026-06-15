<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('code_package_subject')) {
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

        if (Schema::hasTable('user_has_subject')) {
            Schema::table('user_has_subject', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['subject_id']);
                $table->dropUnique(['user_id', 'subject_id']);
            });

            Schema::table('user_has_subject', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                $table->unique(['user_id', 'subject_id', 'unit_id'], 'user_has_subject_user_id_subject_id_unit_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_has_subject')) {
            Schema::table('user_has_subject', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['subject_id']);
                $table->dropUnique('user_has_subject_user_id_subject_id_unit_id_unique');
            });

            Schema::table('user_has_subject', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                $table->unique(['user_id', 'subject_id']);
            });
        }

        if (Schema::hasTable('code_package_subject')) {
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
    }
};
