<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('code_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الحزمة
            $table->date('expires_at'); // تاريخ انتهاء صلاحية الأكواد ضمن الحزمة
            $table->timestamps();
        });

        Schema::create('code_package_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_package_id')->constrained('code_packages')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            

        });
    }

    public function down()
    {
        Schema::dropIfExists('code_packages');
        Schema::dropIfExists('code_package_subject');
    }
};
