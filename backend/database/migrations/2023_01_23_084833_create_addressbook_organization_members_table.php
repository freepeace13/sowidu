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
        Schema::create('addressbook_organization_members', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');

            $table->primary(['user_id', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addressbook_organization_members');
    }
};
