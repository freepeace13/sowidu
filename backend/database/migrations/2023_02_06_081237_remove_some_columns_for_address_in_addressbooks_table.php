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
        Schema::table('addressbooks', function (Blueprint $table) {
            $table->dropColumn([
                'address_country_id',
                'address_state_id',
                'address_city_id',
                'address_zip_code_id',
                'address_street_id',
                'address_house_number_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addressbooks', function (Blueprint $table) {
            $table->unsignedInteger('address_country_id');
            $table->unsignedInteger('address_state_id');
            $table->unsignedInteger('address_city_id');
            $table->unsignedInteger('address_zip_code_id')->nullable();
            $table->unsignedInteger('address_street_id')->nullable();
            $table->unsignedInteger('address_house_number_id')->nullable();
        });
    }
};
