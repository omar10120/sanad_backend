<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const Cities = [
        'damascus',
        'damascus_suburb',
        'homs',
        'hama',
        'aleppo',
        'idlib',
        'tartus',
        'latakia',
        'deir_ezzor',
        'hasaka',
        'raqqa',
        'sweida',
        'daraa',
        'quneitra',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->enum('city',self::Cities)->nullable();
            $table->string('school')->nullable();
            $table->string('photo')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('type_id')->nullable();
            $table->boolean('status')->default(1);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
