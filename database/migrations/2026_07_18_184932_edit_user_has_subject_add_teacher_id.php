<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_has_subject', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_id')->nullable()->after('unit_id');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_has_subject', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->unsignedBigInteger('teacher_id')->nullable(false)->change();
        });
    }
};
