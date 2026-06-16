<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects_video', function (Blueprint $table) {
            $table->string('icon_photo')->nullable()->after('icon');
            $table->string('light_color_code', 7)->nullable()->after('description');
            $table->string('dark_color_code', 7)->nullable()->after('light_color_code');
        });
    }

    public function down(): void
    {
        Schema::table('subjects_video', function (Blueprint $table) {
            $table->dropColumn(['icon_photo', 'light_color_code', 'dark_color_code']);
        });
    }
};
