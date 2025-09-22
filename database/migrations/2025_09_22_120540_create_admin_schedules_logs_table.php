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
        Schema::create('admin_schedules_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin yang melakukan aksi
            $table->string('action'); // create, update, delete
            $table->unsignedBigInteger('schedule_id')->nullable(); // ID schedule yang diakses
            $table->unsignedBigInteger('target_user_id')->nullable(); // ID user yang dijadwalkan
            $table->string('target_user_name')->nullable(); // Nama user untuk referensi
            $table->unsignedBigInteger('shift_id')->nullable(); // ID shift
            $table->string('shift_name')->nullable(); // Nama shift untuk referensi
            $table->date('schedule_date')->nullable(); // Tanggal jadwal
            $table->json('old_values')->nullable(); // Data lama sebelum update
            $table->json('new_values')->nullable(); // Data baru setelah update
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['schedule_id', 'action']);
            $table->index(['target_user_id', 'schedule_date']);
            $table->index('action');
            $table->index('schedule_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_schedules_logs');
    }
};
