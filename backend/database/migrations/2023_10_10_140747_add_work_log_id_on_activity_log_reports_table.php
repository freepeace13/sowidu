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
        Schema::table('activity_log_reports', function (Blueprint $table) {
            $table->dropForeign('activity_log_reports_activity_log_id_foreign');

            $table->foreignId('work_log_id')
                ->nullable()
                ->after('id');

            $table->foreignId('activity_log_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_log_reports', function (Blueprint $table) {
            $table->dropForeign(['work_log_id']);
            $table->dropColumn('work_log_id');
        });
    }
};
