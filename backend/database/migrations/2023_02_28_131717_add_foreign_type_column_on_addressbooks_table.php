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
            $table->tinyInteger('foreign_type')
                ->nullable()
                ->after('model_id');
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
            $table->dropColumn('foreign_type');
        });
    }
};
