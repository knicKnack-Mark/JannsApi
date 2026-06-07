<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('phone')->nullable();
            $table->enum('salary_type', ['Monthly', 'Daily'])->default('Monthly');
            $table->decimal('monthly_salary', 10, 2)->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->enum('attendance', ['Present', 'Absent', 'Not Timed In'])->default('Not Timed In');
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};