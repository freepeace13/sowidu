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
        Schema::defaultStringLength(191);

        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->morphs('sender');
            $table->morphs('recipient');
            $table->string('state');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique([
                'sender_id',
                'sender_type',
                'recipient_id',
                'recipient_type',
                'type',
            ], 'unique');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('invitation_id')->nullable();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedInteger('invitation_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations');

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('invitation_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('invitation_id');
        });
    }
};
