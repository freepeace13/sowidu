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
        Schema::table('deduction_manuals', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->after('id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deduction_manuals', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->after('id')->nullable(false)->change();
        });
    }
};
