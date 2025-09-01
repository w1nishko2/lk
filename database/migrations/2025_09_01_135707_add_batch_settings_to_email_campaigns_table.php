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
            $table->integer('batch_size')->default(100); // Размер пакета для отправки
            $table->integer('delay_between_batches')->default(60); // Задержка между пакетами в секундах
            $table->integer('emails_per_minute')->default(10); // Лимит писем в минуту
            $table->timestamp('next_batch_at')->nullable(); // Время следующего пакета
            $table->integer('current_batch')->default(0); // Текущий пакет
            $table->integer('total_batches')->default(0); // Общее количество пакетов
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'batch_size',
                'delay_between_batches', 
                'emails_per_minute',
                'next_batch_at',
                'current_batch',
                'total_batches'
            ]);
        });
    }
};
