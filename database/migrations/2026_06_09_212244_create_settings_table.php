<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->json('profile')->nullable();
            $table->json('system')->nullable();
            $table->json('attendance')->nullable();
            $table->json('payroll')->nullable();
            $table->json('departments')->nullable();
            $table->json('positions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};