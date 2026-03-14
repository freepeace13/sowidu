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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->unsignedInteger('customer_id')->nullable();
            $table->integer('contractable_id')->nullable();
            $table->string('contractable_type')->nullable();
            $table->integer('order_number')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_draft')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity')->default(1);

            $table->primary(['order_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
