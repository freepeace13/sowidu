<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignId('user_id');
            $table->foreignId('team_id');
            $table->foreignId('client_addressbook_id');
            $table->foreignId('contractor_addressbook_id');
            $table->string('order_number');
            $table->tinyInteger('type');
            $table->text('description');
            $table->tinyInteger('status')->default(OrderStatus::IN_PREPARATION());
            $table->timestamp('order_date');
            $table->timestamp('planned_start_date')->nullable();
            $table->timestamp('planned_finish_date')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
