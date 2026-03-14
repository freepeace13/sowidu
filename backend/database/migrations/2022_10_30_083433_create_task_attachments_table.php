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
        Schema::create('todo_task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id');
            $table->foreignId('author_id');
            $table->foreignId('media_file_id')->nullable();
            $table->string('path')->nullable();
            $table->json('properties');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('todo_tasks')->onDelete('cascade');
            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
        });

        Schema::dropIfExists('task_media');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_task_attachments');
    }
};
