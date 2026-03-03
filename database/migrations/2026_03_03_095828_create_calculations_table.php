<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('calculated_by')->constrained('users'); // executor

            // Payment details
            $table->string('payer_name')->nullable();       // who pays
            $table->string('payer_pinfl')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable(); // actual area m²
            $table->decimal('rate_per_sqm', 12, 2)->nullable(); // price per m²
            $table->decimal('total_amount', 14, 2)->nullable();  // total to pay
            $table->decimal('penalty_amount', 14, 2)->default(0); // penalty
            $table->decimal('paid_amount', 14, 2)->default(0);    // already paid
            $table->date('payment_deadline')->nullable();
            $table->string('payment_period')->nullable();   // e.g. "12 oy"
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
