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
        Schema::table('memberables', function (Blueprint $table) {
            $table->dropUnique('memberables_model_id_model_type_employee_id_unique');
            $table->dropForeign('memberables_employee_id_foreign');
            $table->dropColumn('employee_id');

            $table->morphs('member');

            $table->unique(['model_id', 'model_type', 'member_id', 'member_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memberables', function (Blueprint $table) {
            $table->dropUnique('memberables_model_id_model_type_member_id_member_type_unique');
            $table->dropColumn('member_id');
            $table->dropColumn('member_type');

            $table->unsignedInteger('employee_id');

            $table->unique(['model_id', 'model_type', 'employee_id']);

            $table
                ->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }
};
