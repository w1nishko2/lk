<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignMail;

// Загружаем переменные окружения
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Инициализируем Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Тест подключения к почтовому серверу ===\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "\n";

// Тест 1: Проверка конфигурации Swift Mailer
try {
    $config = config('mail');
    echo "✓ Конфигурация почты загружена успешно\n";
    print_r($config);
} catch (Exception $e) {
    echo "✗ Ошибка загрузки конфигурации: " . $e->getMessage() . "\n";
    exit(1);
}

// Тест 2: Попытка создания транспорта
echo "\n=== Тестирование транспорта ===\n";
try {
    if (env('MAIL_MAILER') === 'sendmail') {
        // Проверяем доступность sendmail
        $sendmailPath = '/usr/sbin/sendmail -bs';
        echo "Проверяем sendmail: $sendmailPath\n";
        
        if (file_exists('/usr/sbin/sendmail')) {
            echo "✓ Sendmail найден\n";
        } else {
            echo "✗ Sendmail не найден в /usr/sbin/sendmail\n";
            
            // Попробуем другие пути
            $paths = ['/usr/bin/sendmail', '/bin/sendmail', '/sbin/sendmail'];
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    echo "✓ Sendmail найден в: $path\n";
                    break;
                }
            }
        }
    } else {
        // SMTP тест
        echo "Тестируем SMTP подключение...\n";
        $socket = @fsockopen(env('MAIL_HOST'), env('MAIL_PORT'), $errno, $errstr, 10);
        if ($socket) {
            echo "✓ SMTP соединение успешно\n";
            fclose($socket);
        } else {
            echo "✗ SMTP соединение неудачно: $errstr ($errno)\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Ошибка транспорта: " . $e->getMessage() . "\n";
}

// Тест 3: Отправка тестового письма
echo "\n=== Тестирование отправки письма ===\n";
try {
    Mail::raw('Тестовое письмо для проверки подключения', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Mail Connection');
    });
    echo "✓ Письмо отправлено успешно (или поставлено в очередь)\n";
} catch (Exception $e) {
    echo "✗ Ошибка отправки письма: " . $e->getMessage() . "\n";
    echo "Детали: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Тест завершен ===\n";
