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

            // 🔥 NEW (replace date & time)
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->integer('guests');
            $table->boolean('videoke')->default(false);

            $table->decimal('amount', 10, 2);
            $table->decimal('paid', 10, 2)->default(0);

            // 🔥 NEW STATUS
            $table->string('status')->default('confirmed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};