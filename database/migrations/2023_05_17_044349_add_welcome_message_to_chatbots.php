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
        Schema::table('chatbots', function (Blueprint $table) {
            // add welcome_message column after trainable_id
            $table->text('welcome_message')->after('trainable_id')->nullable();
            $table->text('welcome_prompts')->after('welcome_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbots', function (Blueprint $table) {
            //
        });
    }
};
