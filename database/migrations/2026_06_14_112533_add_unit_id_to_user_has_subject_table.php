<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_has_subject', function (Blueprint $table) {
            
            $table->unsignedBigInteger('unit_id')->after('subject_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });   
    }

    public function down(): void
    {
        Schema::table('user_has_subject', function (Blueprint $table) {
            $table->dropUnique('user_has_subject_user_id_subject_id_unit_id_unique');
            $table->unique(['user_id', 'subject_id']);  
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};