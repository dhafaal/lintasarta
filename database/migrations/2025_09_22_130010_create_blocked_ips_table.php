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
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->string('reason');
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('blocked_at');
            $table->timestamp('blocked_until')->nullable();
            $table->boolean('is_permanent')->default(false);
            $table->string('blocked_by')->nullable(); // admin yang memblokir
            $table->timestamps();
            
            $table->index(['ip_address', 'blocked_until']);
            $table->index('is_permanent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};
