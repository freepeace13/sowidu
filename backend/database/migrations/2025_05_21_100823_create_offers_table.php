<?php

use App\Enums\OfferStatus;
use App\Enums\OfferType;
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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('internal_id')->nullable();

            $table->foreignId('user_id');
            $table->foreignId('company_id');

            $table->morphs('recipientable');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('type', OfferType::values());
            $table->enum('status', OfferStatus::values());

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->decimal('total_vat', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->timestamp('offer_date')->nullable();

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
        Schema::dropIfExists('offers');
    }
};
