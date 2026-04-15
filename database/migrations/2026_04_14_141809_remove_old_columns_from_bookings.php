<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        if (Schema::hasColumn('bookings', 'date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('date');
            });
        }

        if (Schema::hasColumn('bookings', 'time')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bookings', 'date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->date('date')->nullable();
            });
        }

        if (!Schema::hasColumn('bookings', 'time')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('time')->nullable();
            });
        }
    }
};