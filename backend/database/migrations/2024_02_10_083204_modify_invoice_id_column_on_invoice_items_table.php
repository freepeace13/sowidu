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
        Schema::disableForeignKeyConstraints();
        if (Schema::hasColumn('invoice_items', 'invoice_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('invoice_id');
            });
        }

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('invoice_id')
                ->constrained()
                ->cascadeOnDelete()
                ->after('id');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('invoice_id')
                ->constrained()
                ->cascadeOnDelete()
                ->after('id');
        });
    }
};
