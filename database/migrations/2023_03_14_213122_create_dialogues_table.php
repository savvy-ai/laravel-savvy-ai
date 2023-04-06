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
        Schema::create('dialogues', function (Blueprint $table) {
            $table->uuid('id')
                ->primary()
                ->autoIncrement();

            $table->foreignUuid('agent_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');
            $table->text('prompt');
            $table->string('topic');
            $table->string('classification');

            $table->string('model')->default('gpt-3.5-turbo');
            $table->integer('max_tokens')->default(128);
            $table->float('temperature')->default(0.5);
            $table->float('presence_penalty')->default(0.1);
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
        Schema::dropIfExists('dialogues');
    }
};
