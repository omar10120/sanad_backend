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
        Schema::table('subjects_video', function (Blueprint $table) {
            $table->string('icon')->nullable()->change();
            $table->string('link')->nullable()->change();
            $table->string('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects_video', function (Blueprint $table) {
            $table->string('icon')->nullable(false)->change();
            $table->string('link')->nullable(false)->change();
            $table->string('description')->nullable(false)->change();
        });
    }
};
