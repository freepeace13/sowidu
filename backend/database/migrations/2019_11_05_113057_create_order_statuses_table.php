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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_draft');
            $table->dropColumn('published_at');
        });

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('status_id');
            $table->timestamp('created_at');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');

            $table->foreign('status_id')
                ->references('id')
                ->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('is_draft')->default(true);
            $table->timestamp('published_at')->nullable();
        });

        Schema::dropIfExists('order_statuses');
    }
};
