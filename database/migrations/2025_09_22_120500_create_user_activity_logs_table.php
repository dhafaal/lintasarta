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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // checkin, checkout, request_permission, login, logout
            $table->string('resource_type')->nullable(); // attendances, permissions, auth
            $table->unsignedBigInteger('resource_id')->nullable(); // ID dari resource yang diakses
            $table->string('resource_name')->nullable(); // Nama resource untuk referensi
            $table->json('additional_data')->nullable(); // Data tambahan seperti lokasi, status, dll
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
