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
        Schema::table('delivery_ticket_materials', function (Blueprint $table) {
            $table->double('purchasing_price')->nullable()->after('quantity');
            $table->double('selling_price')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_ticket_materials', function (Blueprint $table) {
            $table->dropColumn(['purchasing_price', 'selling_price']);
        });
    }
};
