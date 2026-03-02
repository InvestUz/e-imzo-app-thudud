<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('qr_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('pkcs7_signature')->nullable();
            $table->string('signer_name')->nullable();
            $table->string('signer_pinfl', 14)->nullable();
            $table->string('signer_inn', 14)->nullable();
            $table->string('signer_organization')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->json('signature_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
