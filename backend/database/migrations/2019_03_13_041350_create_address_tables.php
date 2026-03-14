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
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('abbrev')->nullable();
            $table->timestamps();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('country_id');
            $table->string('name');
            $table->timestamps();
            $table->unique(['name', 'country_id']);

            $table->foreign('country_id')
                ->references('id')
                ->on('countries');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('state_id');
            $table->string('name');
            $table->timestamps();
            $table->unique(['state_id', 'name']);

            $table->foreign('state_id')
                ->references('id')
                ->on('states');
        });

        Schema::create('zipcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('streets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('street_address')->unique();
            $table->timestamps();
        });

        Schema::create('house_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('house_number')->unique();
            $table->timestamps();
        });

        Schema::create('address', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('ownerable');
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('zipcode_id')->nullable();
            $table->unsignedInteger('street_id')->nullable();
            $table->unsignedInteger('house_number_id')->nullable();
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
        Schema::dropIfExists('address');
        Schema::dropIfExists('house_numbers');
        Schema::dropIfExists('streets');
        Schema::dropIfExists('zipcodes');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
