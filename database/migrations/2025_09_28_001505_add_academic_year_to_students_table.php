<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the academic_year column after type_id
        Schema::table('students', function (Blueprint $table) {
            $table->string('academic_year', 9)
                ->default('2025-2026')
                ->nullable(false)
                ->after('type_id');
        });

        // Update existing students: set status to false and academic_year to "2024-2025"
        DB::table('students')
            ->whereNull('deleted_at') // Only update non-deleted students
            ->update([
                'status' => false,
                'academic_year' => '2024-2025'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the academic_year column
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });

        // Restore status to true for all existing students (optional rollback)
        DB::table('students')
            ->whereNull('deleted_at')
            ->update([
                'status' => true
            ]);
    }
};
