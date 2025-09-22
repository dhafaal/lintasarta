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
        Schema::create('admin_permissions_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin yang melakukan aksi
            $table->string('action'); // approve, reject
            $table->unsignedBigInteger('permission_id')->nullable(); // ID permission yang diakses
            $table->unsignedBigInteger('target_user_id')->nullable(); // ID user yang mengajukan izin
            $table->string('target_user_name')->nullable(); // Nama user untuk referensi
            $table->string('permission_type')->nullable(); // Jenis izin (izin, sakit, cuti)
            $table->text('permission_reason')->nullable(); // Alasan izin
            $table->date('permission_date')->nullable(); // Tanggal izin
            $table->string('old_status')->nullable(); // Status lama (pending)
            $table->string('new_status')->nullable(); // Status baru (approved/rejected)
            $table->json('additional_data')->nullable(); // Data tambahan
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['permission_id', 'action']);
            $table->index(['target_user_id', 'permission_date']);
            $table->index('action');
            $table->index('permission_type');
            $table->index(['new_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permissions_logs');
    }
};
