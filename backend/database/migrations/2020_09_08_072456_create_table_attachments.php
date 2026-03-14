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
        Schema::create('deliverables', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('delivery_id');
            $table->timestamp('created_at');

            $table->unique(['model_id', 'model_type', 'delivery_id']);

            $table
                ->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('media_id');
            $table->timestamp('created_at');

            $table->unique(['model_id', 'model_type', 'media_id']);

            $table
                ->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
        });

        Schema::create('itemables', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('item_id');
            $table->integer('quantity')->default(1);
            $table->timestamp('created_at');

            $table->unique(['model_id', 'model_type', 'item_id']);

            $table
                ->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });

        Schema::create('taskables', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('task_id');
            $table->timestamp('created_at');

            $table->unique(['model_id', 'model_type', 'task_id']);

            $table
                ->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
        });

        Schema::create('memberables', function (Blueprint $table) {
            $table->morphs('model');
            $table->unsignedInteger('employee_id');
            $table->timestamp('created_at');

            $table->unique(['model_id', 'model_type', 'employee_id']);

            $table
                ->foreign('employee_id')
                ->references('id')
                ->on('employees')
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
        Schema::dropIfExists('deliverables');
        Schema::dropIfExists('itemables');
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('taskables');
        Schema::dropIfExists('memberables');
    }
};
