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
        Schema::create('company_invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('company_id');
            $table->string('email');
            $table->timestamp('accepted_at')->nullable();
            $table->boolean('declined')->default(false);
            $table->boolean('revoked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_invites');
    }
};
