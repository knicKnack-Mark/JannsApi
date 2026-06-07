<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('payroll_month');
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->enum('status', ['Paid', 'Pending'])->default('Pending');
            $table->timestamps();

            $table->unique(['staff_id', 'payroll_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};