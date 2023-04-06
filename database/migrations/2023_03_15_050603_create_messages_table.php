<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')
                ->primary()
                ->autoIncrement();

            $table->foreignUuid('chat_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignUuid('dialogue_id')
                ->nullable()
                ->constrained();

            $table->string('role');
            $table->text('content');
            $table->json('media')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
