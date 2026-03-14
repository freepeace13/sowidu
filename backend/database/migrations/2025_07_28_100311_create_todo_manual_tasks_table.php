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
        Schema::create('todo_manual_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_task_id')->constrained('todo_tasks')->onDelete('cascade');
            $table->date('start_date');
            $table->date('finish_date');
            $table->timestamps();
        });

        Schema::create('employee_todo_manual_task', function (Blueprint $table) {
            $table->foreignId('todo_manual_task_id')
                ->constrained('todo_manual_tasks')
                ->onDelete('cascade');

            $table->unsignedInteger('employee_id');
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->primary(['todo_manual_task_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_manual_tasks');
    }
};
