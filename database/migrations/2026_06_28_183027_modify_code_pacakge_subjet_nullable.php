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
        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->change();
            $table->foreignId('subject_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('code_package_subject', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable(false)->change();
            $table->foreignId('subject_id')->nullable(false)->change();
        });
    }
};
