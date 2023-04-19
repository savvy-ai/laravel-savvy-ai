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
        // https://platform.openai.com/docs/api-reference/completions/create
        Schema::create('chatbots', function (Blueprint $table) {
            $table->uuid('id')
                ->primary()
                ->autoIncrement();

            $table->foreignUuid('trainable_id')->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbots');
    }
};
