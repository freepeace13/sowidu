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
        Schema::table('invoice_manual_items', function (Blueprint $table) {
            $table->double('quantity', 8, 2)->default(0)->after('unit_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_manual_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
