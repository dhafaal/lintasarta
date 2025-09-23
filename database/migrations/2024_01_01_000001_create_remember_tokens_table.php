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
        Schema::create('remember_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token_hash');
            $table->string('device_fingerprint');
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at');
            $table->boolean('is_revoked')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_revoked']);
            $table->index(['device_fingerprint', 'is_revoked']);
            $table->index(['expires_at', 'is_revoked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remember_tokens');
    }
};
