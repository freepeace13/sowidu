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
        Schema::dropIfExists('tasks');

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('organization');
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->timestamps();
        });

        Schema::create('task_has_members', function (Blueprint $table) {
            $table->morphs('member');
            $table->unsignedInteger('task_id');
            $table->timestamp('created_at');

            $table->index(['member_id', 'member_type'], 'task_has_members_member_id_member_type_index');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');

            $table->primary(['task_id', 'member_id', 'member_type'], 'task_has_members_task_member_type_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_has_members');
        Schema::dropIfExists('tasks');
    }
};
