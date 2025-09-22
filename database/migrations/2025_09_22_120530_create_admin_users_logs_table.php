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
        Schema::create('admin_users_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin yang melakukan aksi
            $table->string('action'); // create, update, delete
            $table->unsignedBigInteger('target_user_id')->nullable(); // ID user yang diakses
            $table->string('target_user_name')->nullable(); // Nama user untuk referensi
            $table->string('target_user_email')->nullable(); // Email user untuk referensi
            $table->string('target_user_role')->nullable(); // Role user (admin, operator, user)
            $table->json('old_values')->nullable(); // Data lama sebelum update
            $table->json('new_values')->nullable(); // Data baru setelah update
            $table->boolean('password_changed')->default(false); // Apakah password diubah
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['target_user_id', 'action']);
            $table->index('action');
            $table->index('target_user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users_logs');
    }
};
