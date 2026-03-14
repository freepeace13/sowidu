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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('meta')->nullable();
            $table->string('matchcode')->nullable()->unique();
            $table->string('currency')->nullable();
            $table->decimal('purchasing_price', 9, 2)->default(0);
            $table->decimal('sale_price', 9, 2)->default(0);
            $table->decimal('price', 9, 2)->default(0);
            $table->boolean('published')->default(true);

            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->timestamps();
        });

        Schema::create('account_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->morphs('account');

            $table->primary(['product_id', 'account_id', 'account_type']);
        });

        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('name');
            $table->unsignedInteger('parent_id')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('symbol')->unique();
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('account_products');
    }
};
