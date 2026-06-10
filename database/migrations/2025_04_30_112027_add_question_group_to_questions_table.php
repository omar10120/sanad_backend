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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('question_group_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->integer('order')->default(0);
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['question_group_id']);
            $table->dropColumn(['question_group_id', 'order']);
        });
    }
};
