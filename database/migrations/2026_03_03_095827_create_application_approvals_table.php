<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            // Step in the workflow
            $table->tinyInteger('step_order');  // 1=moderator 2=complaint_officer 3=lawyer 4=executor 5=district_head
            $table->string('step_role');        // moderator | complaint_officer | lawyer | executor | district_head

            // Who was originally assigned (district employee)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            // Who actually approved (could be regional backup)
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_backup_approval')->default(false); // true if regional backup stepped in

            // Status: pending | approved | rejected | delegated
            $table->string('status')->default('pending');
            $table->text('comments')->nullable();
            $table->text('pkcs7_signature')->nullable(); // approver's E-IMZO signature

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_approvals');
    }
};
