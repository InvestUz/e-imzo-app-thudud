<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Roles: consumer, moderator, complaint_officer, lawyer, executor, district_head, admin, regional_backup
            $table->string('role')->default('consumer')->after('name');
            $table->string('commission_position')->nullable()->after('role');
            // FK to districts added in 095824 after districts table is created
            $table->unsignedBigInteger('district_id')->nullable()->after('commission_position');
            $table->boolean('is_regional_backup')->default(false)->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn(['role', 'commission_position', 'district_id', 'is_regional_backup']);
        });
    }
};
