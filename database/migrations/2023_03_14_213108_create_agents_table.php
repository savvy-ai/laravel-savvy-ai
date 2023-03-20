<?php

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasUuids;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('chatbot_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');
            $table->text('prompt');
            $table->string('classification');

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
        Schema::dropIfExists('agents');
    }
};
