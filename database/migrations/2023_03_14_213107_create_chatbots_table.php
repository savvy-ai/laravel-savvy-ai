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

            $table->text('prompt');

            $table->string('model')->default('gpt-3.5-turbo');
            $table->integer('max_tokens')->default(32);
            $table->float('temperature')->default(0.0);
            $table->float('presence_penalty')->default(0.0);
            $table->float('frequency_penalty')->default(0.0);
            $table->string('stop')->nullable()->default(null);

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
