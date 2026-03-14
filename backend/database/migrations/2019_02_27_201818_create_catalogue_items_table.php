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
        Schema::create('catalogue_items', function (Blueprint $table) {
            $table->unsignedInteger('catalogue_id');
            $table->unsignedInteger('item_id');

            $table->foreign('catalogue_id')
                ->references('id')
                ->on('catalogues');
            $table->foreign('item_id')
                ->references('id')
                ->on('items');
        });

        DB::unprepared('ALTER TABLE `catalogue_items` ADD PRIMARY KEY (  `catalogue_id` ,  `item_id` )');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogue_items');
    }
};
