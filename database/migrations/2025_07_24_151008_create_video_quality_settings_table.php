<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_quality_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('quality', ['144p', '240p', '360p', '480p', '720p', '1080p', '1440p', '2160p'])->unique();
            $table->integer('width');
            $table->integer('height');
            $table->integer('min_bitrate');
            $table->integer('max_bitrate');
            $table->integer('target_bitrate');
            $table->string('codec')->default('h264');
            $table->json('encoding_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        DB::table('video_quality_settings')->insert([
            [
                'quality' => '144p',
                'width' => 256,
                'height' => 144,
                'min_bitrate' => 80000,
                'max_bitrate' => 100000,
                'target_bitrate' => 90000,
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quality' => '240p',
                'width' => 426,
                'height' => 240,
                'min_bitrate' => 150000,
                'max_bitrate' => 200000,
                'target_bitrate' => 175000,
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quality' => '360p',
                'width' => 640,
                'height' => 360,
                'min_bitrate' => 300000,
                'max_bitrate' => 400000,
                'target_bitrate' => 350000,
                'priority' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quality' => '480p',
                'width' => 854,
                'height' => 480,
                'min_bitrate' => 500000,
                'max_bitrate' => 700000,
                'target_bitrate' => 600000,
                'priority' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quality' => '720p',
                'width' => 1280,
                'height' => 720,
                'min_bitrate' => 1500000,
                'max_bitrate' => 2500000,
                'target_bitrate' => 2000000,
                'priority' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'quality' => '1080p',
                'width' => 1920,
                'height' => 1080,
                'min_bitrate' => 3000000,
                'max_bitrate' => 5000000,
                'target_bitrate' => 4000000,
                'priority' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('video_quality_settings');
    }
};
