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
        Schema::create('todo_subscribers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique([
                'board_id',
                'user_id',
            ], 'todo_subscribers_board_user_unique');

            $table->foreign('board_id')
                ->references('id')
                ->on('todo_boards')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_subscribers');
    }
};
