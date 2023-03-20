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
        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('handle')->nullable();

            $table->foreignUuid('trainable_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignUuid('agent_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignUuid('dialogue_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
