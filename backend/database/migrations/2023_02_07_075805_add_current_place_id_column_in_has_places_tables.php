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
            $table->unsignedBigInteger('current_place_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('current_place_id')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('current_place_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('current_place_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('current_place_id');
        });

        Schema::table('addressbooks', function (Blueprint $table) {
            $table->dropColumn('current_place_id');
        });
    }
};
