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
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('provider')->default('yandex')->after('user_id');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium')->after('provider');
            $table->json('settings')->nullable()->after('priority');
            $table->timestamp('last_processed_at')->nullable()->after('next_batch_at');
            
            // Индексы для оптимизации
            $table->index(['status', 'provider']);
            $table->index(['status', 'next_batch_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropIndex(['status', 'provider']);
            $table->dropIndex(['status', 'next_batch_at']);
            $table->dropColumn(['provider', 'priority', 'settings', 'last_processed_at']);
        });
    }
};
