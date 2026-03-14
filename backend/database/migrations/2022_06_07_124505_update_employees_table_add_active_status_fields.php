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
        if (!Schema::hasColumn('employees', 'active_status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('active_status')->nullable();
            });
        }

        if (!Schema::hasColumn('employees', 'last_seen_at')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('employees', 'active_status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('active_status');
            });
        }

        if (Schema::hasColumn('employees', 'last_seen_at')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('last_seen_at');
            });
        }
    }
};
