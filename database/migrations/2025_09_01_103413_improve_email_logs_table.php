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
        Schema::table('email_logs', function (Blueprint $table) {
            // Добавляем новые поля для детального отслеживания
            $table->string('status')->default('pending')->after('error_message'); // pending, sent, delivered, bounced, opened, clicked, complained, unsubscribed
            $table->string('message_id')->nullable()->after('status'); // ID сообщения от провайдера
            $table->json('provider_response')->nullable()->after('message_id'); // Ответ от SMTP провайдера
            $table->timestamp('sent_at')->nullable()->after('provider_response');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
            $table->timestamp('opened_at')->nullable()->after('delivered_at');
            $table->timestamp('clicked_at')->nullable()->after('opened_at');
            $table->timestamp('bounced_at')->nullable()->after('clicked_at');
            $table->string('bounce_reason')->nullable()->after('bounced_at');
            $table->string('user_agent')->nullable()->after('bounce_reason');
            $table->string('ip_address')->nullable()->after('user_agent');
            $table->integer('retry_count')->default(0)->after('ip_address');
            $table->timestamp('next_retry_at')->nullable()->after('retry_count');
            
            // Индексы для производительности
            $table->index('status');
            $table->index('sent_at');
            $table->index('delivered_at');
            $table->index('opened_at');
            $table->index('message_id');
            $table->index(['campaign_id', 'status']);
            $table->index(['subscriber_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'message_id', 'provider_response', 'sent_at', 'delivered_at',
                'opened_at', 'clicked_at', 'bounced_at', 'bounce_reason', 'user_agent',
                'ip_address', 'retry_count', 'next_retry_at'
            ]);
        });
    }
};
