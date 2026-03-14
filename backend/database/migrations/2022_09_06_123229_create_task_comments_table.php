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
        Schema::create('todo_task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id');
            $table->foreignId('author_id');
            $table->text('message');
            $table->foreign('task_id')->references('id')->on('todo_tasks')->onDelete('cascade');
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
        Schema::dropIfExists('todo_task_comments');
    }
};
