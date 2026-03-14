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
        Schema::create('order_media', function (Blueprint $table) {
            $table->unsignedInteger('media_id');
            $table->unsignedInteger('order_id');
            $table->timestamp('created_at');

            $table->foreign('media_id')
                ->references('id')
                ->on('media');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_media');
    }
};
