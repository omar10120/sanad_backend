<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('teacher_id')
                ->nullable()
                ->after('show_all_teachers')
                ->constrained('teachers')
                ->nullOnDelete();
        });

        // Backfill from existing pivot assignments.
        $rows = DB::table('user_has_subject')
            ->whereNotNull('teacher_id')
            ->select('user_id', 'teacher_id')
            ->distinct()
            ->get()
            ->unique('user_id');

        foreach ($rows as $row) {
            DB::table('users')
                ->where('id', $row->user_id)
                ->update([
                    'teacher_id' => $row->teacher_id,
                    'show_all_teachers' => false,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};
