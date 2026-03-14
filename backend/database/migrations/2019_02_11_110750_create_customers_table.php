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
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ownerable_id')->nullable();
            $table->string('ownerable_type')->nullable();
            $table->integer('billerable_id')->nullable();
            $table->string('billerable_type')->nullable();
            $table->unsignedInteger('contact_person_id')->nullable();
            $table->timestamps();

            $table
                ->foreign('contact_person_id')
                ->references('id')
                ->on('customer_contact_persons');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customers AUTO_INCREMENT = 100001;');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
