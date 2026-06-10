<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('light_color_code')->nullable()->after('description');
            $table->string('dark_color_code')->nullable()->after('light_color_code');
            $table->string('icon_photo')->nullable()->after('dark_color_code');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['light_color_code', 'dark_color_code', 'icon_photo']);
        });
    }
};
