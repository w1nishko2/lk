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
            // Добавляем новые поля
            $table->string('status')->default('draft')->after('content'); // draft, scheduled, sending, sent, paused, cancelled
            $table->integer('total_subscribers')->default(0)->after('status');
            $table->integer('sent_count')->default(0)->after('total_subscribers');
            $table->integer('delivered_count')->default(0)->after('sent_count');
            $table->integer('opened_count')->default(0)->after('delivered_count');
            $table->integer('clicked_count')->default(0)->after('opened_count');
            $table->integer('bounced_count')->default(0)->after('clicked_count');
            $table->integer('unsubscribed_count')->default(0)->after('bounced_count');
            $table->timestamp('scheduled_at')->nullable()->after('unsubscribed_count');
            $table->timestamp('started_at')->nullable()->after('scheduled_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->json('settings')->nullable()->after('completed_at'); // Настройки кампании
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->after('settings');
            
            // Индексы
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn([
                'status', 'total_subscribers', 'sent_count', 'delivered_count', 
                'opened_count', 'clicked_count', 'bounced_count', 'unsubscribed_count',
                'scheduled_at', 'started_at', 'completed_at', 'settings', 'template_id'
            ]);
        });
    }
};
