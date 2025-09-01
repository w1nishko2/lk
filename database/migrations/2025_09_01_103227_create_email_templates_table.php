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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название шаблона
            $table->string('slug')->unique(); // URL-friendly название
            $table->string('subject'); // Тема письма
            $table->longText('html_content'); // HTML содержимое
            $table->longText('text_content')->nullable(); // Текстовая версия
            $table->json('variables')->nullable(); // Доступные переменные
            $table->string('category')->default('general'); // Категория
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Дополнительные данные
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
