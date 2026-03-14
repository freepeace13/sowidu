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
        Schema::table('avatars', function (Blueprint $table) {
            $table->dropColumn('image_name');
            $table->dropColumn('id');
            $table->dropColumn('created_at');

            $table->unsignedInteger('media_id');

            $table->foreign('media_id')
                ->references('id')
                ->on('media');

            $table->primary([
                'ownerable_id',
                'ownerable_type',
            ], 'avatars_ownerable_id_ownerable_type_primary');

            $table->unique([
                'ownerable_id',
                'ownerable_type',
                'media_id',
            ], 'ownerable_id_ownerable_type_media_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('avatars', function (Blueprint $table) {
            $table->dropForeign('avatars_media_id_foreign');
            $table->dropPrimary('avatars_ownerable_id_ownerable_type_primary');
            $table->dropUnique('ownerable_id_ownerable_type_media_id_unique');

            $table->dropColumn('media_id');

            $table->integer('id');
            $table->timestamp('created_at')->nullable();
            $table->text('image_name')->nullable();
        });

        Schema::table('avatars', function (Blueprint $table) {
            $table->integer('id')->autoIncrements()->change();
            $table->primary('id');
        });
    }
};
