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
        Schema::table('invoices', function (Blueprint $table) {
            Schema::table('invoices', function (Blueprint $table) {
                // Add a composite index to company_id and created_at
                $table->index(['company_id', 'created_at']);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop the composite index
            $table->dropIndex(['company_id', 'created_at']);
        });
    }
};
