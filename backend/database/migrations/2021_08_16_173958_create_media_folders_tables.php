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
        Schema::create('media_folders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->morphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('name');
            $table->string('disk');
            $table->text('custom_properties');
            $table->unsignedBigInteger('folder_id')->nullable();

            $table->nullableTimestamps();
        });

        Schema::table('media_files', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_folders');

        Schema::table('media_files', function (Blueprint $table) {
            $table->dropColumn('folder_id');
        });
    }
};
