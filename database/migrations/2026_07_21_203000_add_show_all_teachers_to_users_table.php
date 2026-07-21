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
            $table->boolean('show_all_teachers')->default(false)->after('status');
        });

        // Preserve previous behavior: no assigned teacher_id meant "see all teachers".
        $restrictedUserIds = DB::table('user_has_subject')
            ->whereNotNull('teacher_id')
            ->distinct()
            ->pluck('user_id');

        DB::table('users')
            ->whereNotIn('id', $restrictedUserIds)
            ->update(['show_all_teachers' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_all_teachers');
        });
    }
};
