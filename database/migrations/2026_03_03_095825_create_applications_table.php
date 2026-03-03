<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();          // App number e.g. ARZ-2026-00001
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts');  // Which district the land belongs to

            // Cadastral info
            $table->string('cadastral_number');
            $table->text('address')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();  // requested adjacent area m²

            // Application source
            $table->enum('source', ['online', 'written'])->default('online');
            $table->text('description')->nullable();

            // Status tracking
            // pending → moderator_review → complaint_review → legal_review → executor_review → head_review → approved/rejected
            $table->string('status')->default('pending');
            $table->string('current_step')->nullable(); // moderator, complaint_officer, lawyer, executor, district_head

            // Applicant's E-IMZO signature on application
            $table->text('applicant_pkcs7')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
