<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dalolatnoma_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            // One of: hokim_qurilish, qurilish, ekologiya, obodonlashtirish,
            //         kadastr, fvv, ses, soliq, iib, yordamchi
            $table->string('commission_position');

            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('pkcs7_signature');
            $table->string('qr_code')->unique();   // UUID for public verification
            $table->timestamp('signed_at');
            $table->timestamps();

            // One signature per position per application (updatable)
            $table->unique(['application_id', 'commission_position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dalolatnoma_signatures');
    }
};
