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
            $table->string('email')->nullable();
            $table->string('institution_type')->nullable();
            $table->dropColumn('jobtitle');
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
            $table->dropColumn('email');
            $table->dropColumn('institution_type');
            $table->string('jobtitle')->nullable();
        });
    }
};
