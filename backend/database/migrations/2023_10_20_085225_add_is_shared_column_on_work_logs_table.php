<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false)->after('duration_in_seconds');
        });
    }

    public function down()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
    }
};
