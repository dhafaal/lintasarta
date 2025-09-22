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
        Schema::create('admin_shifts_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // create, update, delete
            $table->unsignedBigInteger('shift_id')->nullable(); // ID shift yang diakses
            $table->string('shift_name')->nullable(); // Nama shift untuk referensi
            $table->string('shift_category')->nullable(); // Kategori shift (Pagi, Siang, Malam)
            $table->json('old_values')->nullable(); // Data lama sebelum update
            $table->json('new_values')->nullable(); // Data baru setelah update
            $table->text('description')->nullable(); // Deskripsi aktivitas
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['shift_id', 'action']);
            $table->index('action');
            $table->index('shift_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_shifts_logs');
    }
};
