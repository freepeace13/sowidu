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
        Schema::dropIfExists('verifications');

        Schema::create('verifications', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('code', 6);
            $table->string('vouch', 60);
            $table->string('message');
            $table->dateTime('verified_at')->nullable();
            $table->tinyInteger('revoked')->default(false);
            $table->dateTime('expires_at');
            $table->timestamp('created_at');
        });

        if (Schema::hasColumn('users', 'active_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('active_status');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('active_status')->default('offline');
            $table->string('password')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('users');

            if ($doctrineTable->hasIndex('users_email_unique')) {
                $table->dropUnique('users_email_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('users');

            if (!$doctrineTable->hasIndex('users_email_unique')) {
                $table->unique('email');
            }

            $table->string('password')->change();
        });
    }
};
