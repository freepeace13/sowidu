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
        Schema::create('addressbooks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('legalform')->nullable();
            $table->string('jobtitle')->nullable();
            $table->nullableMorphs('model');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('address_country_id');
            $table->unsignedInteger('address_state_id');
            $table->unsignedInteger('address_city_id');
            $table->unsignedInteger('address_zip_code_id')->nullable();
            $table->unsignedInteger('address_street_id')->nullable();
            $table->unsignedInteger('address_house_number_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('addressbooks');
    }
};
