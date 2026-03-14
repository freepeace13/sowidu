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
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('construction_site_id')->nullable()->after('notes')->constrained('places');
            $table->timestamp('execution_period_start')->nullable()->after('construction_site_id');
            $table->timestamp('execution_period_end')->nullable()->after('execution_period_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['construction_site_id']);
            $table->dropColumn([
                'construction_site_id',
                'execution_period_start',
                'execution_period_end',
            ]);
        });
    }
};
