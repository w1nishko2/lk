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
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->string('status')->default('pending')->change();
            $table->index(['campaign_id', 'status']);
            $table->index(['status', 'retry_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn(['retry_count', 'last_retry_at']);
            $table->dropIndex(['campaign_id', 'status']);
            $table->dropIndex(['status', 'retry_count']);
        });
    }
};
