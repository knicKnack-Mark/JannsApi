<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->integer('max_pax')
                ->default(0)
                ->after('guests');

            $table->integer('extra_pax')
                ->default(0)
                ->after('max_pax');

            $table->decimal('extra_pax_rate', 10, 2)
                ->default(100)
                ->after('extra_pax');

            $table->decimal('extra_pax_discount', 10, 2)
                ->default(0)
                ->after('extra_pax_rate');

            $table->decimal('extra_pax_total', 10, 2)
                ->default(0)
                ->after('extra_pax_discount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->dropColumn([
                'max_pax',
                'extra_pax',
                'extra_pax_rate',
                'extra_pax_discount',
                'extra_pax_total'
            ]);
        });
    }
};