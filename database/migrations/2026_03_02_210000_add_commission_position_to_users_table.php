<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // commission_position is now added in 2026_03_03_095823_add_role_district_to_users_table
        // This migration is kept as a no-op to preserve migration history
    }

    public function down(): void
    {
        // no-op
    }
};
