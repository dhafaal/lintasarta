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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('device_fingerprint')->nullable();
            $table->boolean('is_trusted_device')->default(false);
            $table->timestamp('last_activity');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['user_id', 'last_activity']);
            $table->index(['session_id', 'expires_at']);
            $table->index('is_trusted_device');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
