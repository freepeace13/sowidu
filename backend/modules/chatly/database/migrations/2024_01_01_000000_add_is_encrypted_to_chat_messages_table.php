<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Musonza\Chat\ConfigurationManager;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('musonza_chat.database_connection');

        $schema = $connection ? Schema::connection($connection) : Schema::getFacadeRoot();

        if (!$schema->hasColumn(ConfigurationManager::MESSAGES_TABLE, 'is_encrypted')) {
            $schema->table(ConfigurationManager::MESSAGES_TABLE, function (Blueprint $table) {
                $table->boolean('is_encrypted')->default(false)->after('data');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('musonza_chat.database_connection');

        $schema = $connection ? Schema::connection($connection) : Schema::getFacadeRoot();

        if ($schema->hasColumn(ConfigurationManager::MESSAGES_TABLE, 'is_encrypted')) {
            $schema->table(ConfigurationManager::MESSAGES_TABLE, function (Blueprint $table) {
                $table->dropColumn('is_encrypted');
            });
        }
    }
};
