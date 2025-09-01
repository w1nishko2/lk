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
        Schema::table('email_subscribers', function (Blueprint $table) {
            // Добавляем новые поля
            $table->string('first_name')->nullable()->after('email');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('status')->default('active')->after('last_name'); // active, unsubscribed, bounced, complained
            $table->json('tags')->nullable()->after('status'); // Теги для сегментации
            $table->json('custom_fields')->nullable()->after('tags'); // Дополнительные поля
            $table->timestamp('subscribed_at')->nullable()->after('custom_fields');
            $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            $table->string('unsubscribe_reason')->nullable()->after('unsubscribed_at');
            $table->string('source')->nullable()->after('unsubscribe_reason'); // Источник подписки
            $table->timestamp('last_activity_at')->nullable()->after('source');
            $table->string('unsubscribe_token')->unique()->nullable()->after('last_activity_at');
            
            // Индексы
            $table->index('status');
            $table->index('subscribed_at');
            $table->index('last_activity_at');
            $table->index('unsubscribe_token');
        });
    }

    public function down(): void
    {
        Schema::table('email_subscribers', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'status', 'tags', 'custom_fields',
                'subscribed_at', 'unsubscribed_at', 'unsubscribe_reason',
                'source', 'last_activity_at', 'unsubscribe_token'
            ]);
        });
    }
};
