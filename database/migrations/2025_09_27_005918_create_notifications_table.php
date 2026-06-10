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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('type', ['general', 'question_update', 'system', 'announcement'])->default('general');
            $table->enum('target_type', ['all', 'type', 'student', 'custom'])->default('all');
            $table->json('target_ids')->nullable(); // For specific targets
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['draft', 'sent', 'scheduled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('successful_sends')->default(0);
            $table->integer('failed_sends')->default(0);
            $table->json('send_results')->nullable(); // Store detailed send results
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'scheduled_at']);
            $table->index(['target_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
