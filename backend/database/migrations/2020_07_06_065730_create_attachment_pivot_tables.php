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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('delivery_id');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');
        });

        Schema::create('order_tasks', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('task_id');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');
        });

        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('delivery_id');
            $table->timestamps();

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');
        });

        Schema::create('delivery_media', function (Blueprint $table) {
            $table->unsignedInteger('delivery_id');
            $table->unsignedInteger('media_id');
            $table->timestamps();

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
        });

        Schema::create('task_media', function (Blueprint $table) {
            $table->unsignedInteger('media_id');
            $table->unsignedInteger('task_id');
            $table->timestamps();

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
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
        Schema::dropIfExists('order_tasks');
        Schema::dropIfExists('order_deliveries');
        Schema::dropIfExists('delivery_tasks');
        Schema::dropIfExists('delivery_media');
        Schema::dropIfExists('task_media');
    }
};
