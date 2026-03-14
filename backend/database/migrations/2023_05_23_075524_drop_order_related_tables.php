<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::dropIfExists('deliverables');
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('delivery_media');
        Schema::dropIfExists('delivery_members');
        Schema::dropIfExists('delivery_tasks');

        Schema::dropIfExists('order_deliveries');
        Schema::dropIfExists('deliveries');

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_media');
        Schema::dropIfExists('order_members');
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('order_tasks');
        Schema::dropIfExists('orders');

        Schema::dropIfExists('itemables');
        Schema::dropIfExists('item_media');
        Schema::dropIfExists('catalogue_items');
        Schema::dropIfExists('items');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
