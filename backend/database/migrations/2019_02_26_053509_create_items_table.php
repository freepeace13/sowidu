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
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('ownerable');
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('item_type_id');
            $table->string('name', 45);
            $table->text('long_description')->nullable();
            $table->decimal('offered_price', 13, 2)->default(0.00);
            $table->decimal('fix_traded_price', 13, 2)->default(0.00);
            $table->decimal('retail_price', 13, 2)->default(0.00);

            $table->foreign('unit_id')
                ->references('id')
                ->on('units');
            $table->foreign('item_type_id')
                ->references('id')
                ->on('item_types');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
