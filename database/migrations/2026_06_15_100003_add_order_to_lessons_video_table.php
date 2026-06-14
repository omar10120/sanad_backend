<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons_video', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(1)->after('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('lessons_video', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
