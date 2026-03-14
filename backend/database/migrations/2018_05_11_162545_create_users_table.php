<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 100)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->char('gender', 10)->nullable();
            $table->string('email', 60)->unique()->nullable();
            $table->string('password', 60);
            $table->string('mobile', 20)->nullable();
            $table->tinyInteger('confirmed')->default(0);
            $table->enum('active_status', ['online', 'offline', 'idle'])->default('offline');
            $table->rememberToken();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1001;');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
