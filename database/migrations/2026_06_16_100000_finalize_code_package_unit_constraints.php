<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if a foreign key exists on a given table and column.
     */
    private function foreignKeyExists(string $table, string $column): bool
    {
        $database = DB::getDatabaseName();
        $result = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$database, $table, $column]
        );

        return ! empty($result);
    }

    /**
     * Drop a foreign key by column name if it exists.
     */
    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        if (! $this->foreignKeyExists($table, $column)) {
            return;
        }

        // Laravel's dropForeign(['column']) uses the default naming convention.
        // We'll try that first, and if it fails, fallback to information_schema lookup.
        try {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->dropForeign([$column]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // If the default name fails, fetch the actual constraint name and drop it.
            $database = DB::getDatabaseName();
            $constraint = DB::selectOne(
                "SELECT CONSTRAINT_NAME
                 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$database, $table, $column]
            );

            if ($constraint) {
                Schema::table($table, function (Blueprint $table) use ($constraint) {
                    $table->dropForeign($constraint->CONSTRAINT_NAME);
                });
            }
        }
    }

    public function up(): void
    {
        if (Schema::hasTable('code_package_subject')) {
            // Safely drop the existing foreign key on subject_id
            $this->dropForeignKeyIfExists('code_package_subject', 'subject_id');

            // Remove orphaned rows that would violate the new constraint
            $validSubjectIds = DB::table('subjects_video')->pluck('id');
            DB::table('code_package_subject')
                ->whereNotIn('subject_id', $validSubjectIds)
                ->delete();

            // Add the new foreign key referencing subjects_video
            Schema::table('code_package_subject', function (Blueprint $table) {
                $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects_video')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('user_has_subject')) {
            // Drop existing foreign keys and unique constraint
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
            // Safely drop the new foreign key
            $this->dropForeignKeyIfExists('code_package_subject', 'subject_id');

            // Restore the old foreign key referencing subjects
            Schema::table('code_package_subject', function (Blueprint $table) {
                $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects')
                    ->onDelete('cascade');
            });
        }
    }
};