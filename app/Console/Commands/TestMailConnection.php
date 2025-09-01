<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignMail;
use App\Models\EmailTemplate;
use App\Models\EmailSubscriber;

class TestMailConnection extends Command
{
    protected $signature = 'test:mail {--email= : Email address to send test to}';
    protected $description = 'Test email connection and send a test email';

    public function handle()
    {
        $this->info('=== 📧 ТЕСТ ПОДКЛЮЧЕНИЯ К ПОЧТОВОМУ СЕРВЕРУ ===');
        $this->newLine();

        // Показываем текущие настройки
        $this->displayCurrentSettings();
        
        // Тестируем подключение
        if (!$this->testConnection()) {
            return 1;
        }

        // Отправляем тестовое письмо
        return $this->sendTestEmail();
    }

    protected function displayCurrentSettings(): void
    {
        $this->info('🔧 ТЕКУЩИЕ НАСТРОЙКИ:');
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host', config('mail.mailers.sendmail.path', 'sendmail')));
        $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port', 'N/A'));
        $this->line('MAIL_ENCRYPTION: ' . (config('mail.mailers.smtp.encryption') ?: 'none'));
        $this->line('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->line('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        $this->newLine();
    }

    protected function testConnection(): bool
    {
        $this->info('🔍 ТЕСТИРОВАНИЕ ПОДКЛЮЧЕНИЯ...');
        
        $mailer = config('mail.default');
        
        if ($mailer === 'sendmail') {
            $this->info('✓ Используется sendmail - подключение не требуется');
            return true;
        }

        try {
            $transport = app('mailer')->getSymfonyTransporter();
            
            if (method_exists($transport, 'start')) {
                $transport->start();
                $this->info('✓ Подключение к SMTP серверу успешно');
                return true;
            } else {
                $this->info('✓ Транспорт готов к отправке');
                return true;
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Ошибка подключения: ' . $e->getMessage());
            $this->provideTroubleshootingTips($e->getMessage());
            return false;
        }
    }

    protected function sendTestEmail(): int
    {
        $email = $this->option('email') ?: $this->ask('Введите email для тестовой отправки:', 'test@example.com');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('✗ Неверный формат email адреса');
            return 1;
        }

        $this->info("📤 ОТПРАВКА ТЕСТОВОГО ПИСЬМА НА: {$email}");

        try {
            // Создаем тестовый шаблон
            $testTemplate = new EmailTemplate([
                'name' => 'Test Template',
                'subject' => 'Тест подключения к почтовому серверу',
                'html_content' => $this->getTestEmailTemplate(),
                'variables' => []
            ]);

            // Создаем фиктивного подписчика
            $testSubscriber = new EmailSubscriber([
                'email' => $email,
                'name' => 'Test User',
                'is_subscribed' => true
            ]);

            // Отправляем письмо
            Mail::to($email)->send(new CampaignMail($testTemplate, $testSubscriber, null));
            
            $this->info('✓ Тестовое письмо отправлено успешно!');
            $this->newLine();
            $this->info('📊 РЕКОМЕНДАЦИИ:');
            $this->line('• Проверьте папку "Спам" если письмо не пришло');
            $this->line('• Настройте SPF, DKIM и DMARC записи для лучшей доставляемости');
            $this->line('• Используйте выделенный IP для массовых рассылок');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('✗ Ошибка отправки: ' . $e->getMessage());
            $this->provideTroubleshootingTips($e->getMessage());
            return 1;
        }
    }

    protected function provideTroubleshootingTips(string $error): void
    {
        $this->newLine();
        $this->warn('🔧 РЕКОМЕНДАЦИИ ПО УСТРАНЕНИЮ ПРОБЛЕМ:');

        if (str_contains($error, 'Connection refused')) {
            $this->line('• SMTP сервер недоступен или заблокирован');
            $this->line('• Проверьте MAIL_HOST и MAIL_PORT в файле .env');
            $this->line('• Убедитесь что порт не заблокирован файрволом');
            $this->line('• Попробуйте альтернативные порты (587, 2525, 25)');
            $this->line('• Рассмотрите использование sendmail: MAIL_MAILER=sendmail');
        } elseif (str_contains($error, 'Authentication failed')) {
            $this->line('• Неверные учетные данные SMTP');
            $this->line('• Проверьте MAIL_USERNAME и MAIL_PASSWORD в файле .env');
            $this->line('• Включите "Менее безопасные приложения" (для Gmail)');
            $this->line('• Используйте пароли приложений (для Gmail, Yandex)');
        } elseif (str_contains($error, 'SSL') || str_contains($error, 'TLS')) {
            $this->line('• Проблемы с шифрованием SSL/TLS');
            $this->line('• Попробуйте MAIL_ENCRYPTION=null для отключения шифрования');
            $this->line('• Или измените на MAIL_ENCRYPTION=tls');
            $this->line('• Проверьте что порт соответствует типу шифрования');
        } else {
            $this->line('• Проверьте логи Laravel: storage/logs/laravel.log');
            $this->line('• Убедитесь что все настройки почты корректны');
            $this->line('• Попробуйте использовать sendmail: MAIL_MAILER=sendmail');
            $this->line('• Рассмотрите использование внешних SMTP провайдеров');
        }

        $this->newLine();
        $this->info('📋 АЛЬТЕРНАТИВНЫЕ SMTP ПРОВАЙДЕРЫ:');
        $this->line('• Gmail: smtp.gmail.com:587 (TLS)');
        $this->line('• Yandex: smtp.yandex.ru:587 (TLS)');
        $this->line('• Mail.ru: smtp.mail.ru:587 (TLS)');
        $this->line('• Mailgun: smtp.mailgun.org:587 (TLS)');
        $this->line('• SendGrid: smtp.sendgrid.net:587 (TLS)');

        $this->newLine();
        $this->info('⚡ БЫСТРОЕ ИСПРАВЛЕНИЕ:');
        $this->line('php artisan config:clear && php artisan queue:restart');
    }

    protected function getTestEmailTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест подключения</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #cce7ff; border: 1px solid #99d6ff; color: #004085; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 12px; color: #6c757d; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Тест подключения успешен!</h1>
        <p>Email система работает корректно</p>
    </div>
    
    <div class="content">
        <div class="success">
            <strong>🎉 Поздравляем!</strong> Ваша email система настроена и работает правильно. Это тестовое письмо подтверждает, что отправка email работает корректно.
        </div>
        
        <h3>📊 Информация о системе:</h3>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">' . now()->format('H:i') . '</div>
                <div class="stat-label">Время отправки</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . config('mail.default') . '</div>
                <div class="stat-label">Mail Driver</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . config('mail.from.address') . '</div>
                <div class="stat-label">From Address</div>
            </div>
        </div>
        
        <div class="info">
            <strong>ℹ️ Следующие шаги:</strong>
            <ul>
                <li>Настройте SPF, DKIM и DMARC записи для домена</li>
                <li>Добавьте подписчиков в базу данных</li>
                <li>Создайте email шаблоны для рассылок</li>
                <li>Настройте мониторинг доставляемости</li>
            </ul>
        </div>
        
        <p><strong>Дата отправки:</strong> ' . now()->format('d.m.Y H:i:s') . '</p>
        <p><strong>Сервер:</strong> ' . config('app.name') . '</p>
        <p><strong>Версия Laravel:</strong> ' . app()->version() . '</p>
    </div>
    
    <div class="footer">
        <p>Это автоматически сгенерированное тестовое письмо<br>
        <small>Отправлено из системы управления email кампаниями</small></p>
    </div>
</body>
</html>';
    }
}
