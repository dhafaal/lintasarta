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
        Schema::create('schedule_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requested_schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending_target', 'rejected_by_target', 'pending_admin', 'rejected_by_admin', 'approved', 'canceled'])->default('pending_target');
            $table->text('target_rejection_reason')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_swap_requests');
    }
};
