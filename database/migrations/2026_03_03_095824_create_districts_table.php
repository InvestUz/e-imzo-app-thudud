<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. Chilonzor
            $table->string('name_uz');        // Uzbek name
            $table->string('code')->unique(); // e.g. CHI, OLM, SHY ...
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Now that districts exists, add FK on users.district_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
        });
        Schema::dropIfExists('districts');
    }
};
