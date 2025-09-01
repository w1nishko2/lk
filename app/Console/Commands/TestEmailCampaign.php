<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;

class TestEmailCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email адрес для теста}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование настроек email рассылки';

    /**
     * Execute the console command.
     */
    public function handle(EmailService $emailService)
    {
        $this->info('🚀 Тестирование настроек email...');
        
        // Получаем email из аргумента или запрашиваем у пользователя
        $email = $this->argument('email') ?: $this->ask('Введите email для теста');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Некорректный email адрес');
            return 1;
        }

        try {
            // Отправляем тестовое письмо
            Mail::send([], [], function ($message) use ($email) {
                $message->to($email)
                       ->subject('🧪 Тест настроек почты - Конструктор Сайтов')
                       ->from(config('mail.from.address'), config('mail.from.name'))
                       ->html($this->getTestEmailContent());
            });

            $this->info("✅ Тестовое письмо успешно отправлено на: {$email}");
            $this->info('📧 Проверьте почтовый ящик (включая папку спам)');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка отправки: ' . $e->getMessage());
            
            $this->newLine();
            $this->warn('🔧 Проверьте настройки в .env файле:');
            $this->line('MAIL_HOST=' . config('mail.mailers.smtp.host'));
            $this->line('MAIL_PORT=' . config('mail.mailers.smtp.port'));
            $this->line('MAIL_USERNAME=' . config('mail.mailers.smtp.username'));
            $this->line('MAIL_ENCRYPTION=' . config('mail.mailers.smtp.encryption'));
            
            return 1;
        }
    }
    
    private function getTestEmailContent(): string
    {
        return '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест настроек почты</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #28a745; margin-bottom: 20px; }
        .header h1 { color: #28a745; margin: 0; }
        .content { padding: 20px 0; }
        .success { background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin: 20px 0; }
        .info { background-color: #d1ecf1; padding: 15px; border-radius: 5px; border-left: 4px solid #0dcaf0; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Тест прошел успешно!</h1>
            <p>Настройки почты работают корректно</p>
        </div>
        
        <div class="content">
            <div class="success">
                <h3>🎉 Поздравляем!</h3>
                <p>Ваши настройки email работают правильно. Система готова к отправке рассылок.</p>
            </div>
            
            <div class="info">
                <h4>📋 Информация о тестовой отправке:</h4>
                <ul>
                    <li><strong>Дата отправки:</strong> ' . now()->format('d.m.Y H:i:s') . '</li>
                    <li><strong>SMTP сервер:</strong> ' . config('mail.mailers.smtp.host') . '</li>
                    <li><strong>Порт:</strong> ' . config('mail.mailers.smtp.port') . '</li>
                    <li><strong>Шифрование:</strong> ' . strtoupper(config('mail.mailers.smtp.encryption')) . '</li>
                    <li><strong>От кого:</strong> ' . config('mail.from.name') . ' &lt;' . config('mail.from.address') . '&gt;</li>
                </ul>
            </div>
            
            <p>Теперь вы можете приступать к созданию email кампаний в системе рассылок.</p>
        </div>
        
        <div class="footer">
            <p>Это автоматически сгенерированное тестовое письмо</p>
            <p>Конструктор Сайтов - создайте сайт за 15 минут!</p>
        </div>
    </div>
</body>
</html>';
    }
}
