<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_links_video', function (Blueprint $table) {
            $table->id();
        
            
            $table->unsignedBigInteger('lesson_video_id');
        
            $table->string('name');
        
            $table->integer('order')->default(1);
        
            $table->string('youtube_link');
        
            $table->time('video_time')->nullable(); 
            $table->timestamps();
        
      
         
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_links_video');
    }
};