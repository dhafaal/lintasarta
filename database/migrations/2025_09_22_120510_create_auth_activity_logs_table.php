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
        Schema::create('auth_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // login, logout, failed_login, password_reset
            $table->string('email')->nullable(); // Email yang digunakan untuk login
            $table->string('status'); // success, failed, blocked
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('attempted_at')->nullable(); // Waktu percobaan login
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['email', 'created_at']);
            $table->index(['action', 'status']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_activity_logs');
    }
};
