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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->dateTime('schedule');
            $table->string('title')->nullable();
            $table->longText('remarks')->nullable();
            $table->morphs('contractable');
            $table->unsignedInteger('customer_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
        });

        Schema::create('delivery_items', function (Blueprint $table) {
            $table->unsignedInteger('delivery_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity')->nullable();

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });

        Schema::create('delivery_members', function (Blueprint $table) {
            $table->unsignedInteger('delivery_id');
            $table->morphs('member');
            $table->timestamp('created_at');

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')
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
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('delivery_members');
        Schema::dropIfExists('deliveries');
    }
};
