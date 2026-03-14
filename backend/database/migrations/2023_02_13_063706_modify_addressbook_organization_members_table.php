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
        Schema::dropIfExists('addressbook_organization_members');

        Schema::create('addressbook_organization_members', function (Blueprint $table) {
            $table->unsignedBigInteger('addressbook_member_id');
            $table->unsignedBigInteger('addressbook_organization_id');
            $table->string('position')->nullable();

            $table->foreign('addressbook_member_id', 'addrbook_org_members_addrbook_member_id_foreign')
                ->references('id')
                ->on('addressbooks')
                ->onDelete('cascade');

            $table->foreign('addressbook_organization_id', 'addrbook_org_members_addrbook_org_id_foreign')
                ->references('id')
                ->on('addressbooks')
                ->onDelete('cascade');
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
        Schema::create('addressbook_organization_members', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');

            $table->primary(['user_id', 'organization_id']);
        });
    }
};
