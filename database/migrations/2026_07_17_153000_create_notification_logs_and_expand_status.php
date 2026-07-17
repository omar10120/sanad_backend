<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('status', 20)->default('failed');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'status']);
            $table->index(['student_id', 'created_at']);
        });

        // Expand status values for async sending
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN status ENUM('draft', 'sent', 'scheduled', 'processing', 'failed') DEFAULT 'draft'");
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('status', 20)->default('draft')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('notifications')
                ->whereIn('status', ['processing', 'failed'])
                ->update(['status' => 'draft']);

            DB::statement("ALTER TABLE notifications MODIFY COLUMN status ENUM('draft', 'sent', 'scheduled') DEFAULT 'draft'");
        }
    }
};
