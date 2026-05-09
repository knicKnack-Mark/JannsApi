<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('address');
            $table->string('cabin');

            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->integer('guests');

            // NEW
            $table->integer('max_pax')->default(0);

            $table->integer('extra_pax')->default(0);

            $table->decimal('extra_pax_rate', 10, 2)->default(100);

            $table->decimal('extra_pax_discount', 10, 2)->default(0);

            $table->decimal('extra_pax_total', 10, 2)->default(0);

            $table->boolean('videoke')->default(false);

            $table->decimal('amount', 10, 2);

            $table->decimal('paid', 10, 2)->default(0);

            $table->string('status')->default('confirmed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};