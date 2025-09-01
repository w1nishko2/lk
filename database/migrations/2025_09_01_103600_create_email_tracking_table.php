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
        Schema::create('email_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('email_logs')->onDelete('cascade');
            $table->string('event_type'); // open, click, bounce, complaint, unsubscribe
            $table->string('url')->nullable(); // для кликов
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable(); // Геолокация
            $table->json('metadata')->nullable(); // Дополнительные данные
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->index(['log_id', 'event_type']);
            $table->index('occurred_at');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_tracking');
    }
};
