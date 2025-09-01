#!/bin/bash

# Скрипт быстрой проверки системы email-кампаний
echo "=== Проверка системы email-кампаний ==="

# Проверка конфигурации Laravel
echo "1. Проверка конфигурации Laravel..."
php artisan config:show | grep -E "(APP_ENV|DB_DATABASE|MAIL_MAILER|QUEUE_CONNECTION)"

echo ""
echo "2. Проверка подключения к базе данных..."
php artisan migrate:status

echo ""
echo "3. Проверка шаблонов email..."
php artisan tinker --execute="echo App\Models\EmailTemplate::count() . ' шаблонов найдено';"

echo ""
echo "4. Проверка очередей..."
php artisan queue:monitor

echo ""
echo "5. Проверка заданий в очереди..."
php artisan queue:work --once --queue=default,medium-email,bulk-email

echo ""
echo "6. Тест создания тестовой кампании..."
php artisan tinker --execute="
\$campaign = new App\Models\EmailCampaign([
    'name' => 'Test Campaign',
    'subject' => 'Тестовое письмо',
    'template_id' => 1,
    'status' => 'draft',
    'total_recipients' => 10
]);
\$campaign->save();
echo 'Тестовая кампания создана с ID: ' . \$campaign->id;
"

echo ""
echo "=== Проверка завершена ==="
echo ""
echo "Для запуска полного теста выполните:"
echo "php artisan email:queue-monitor"
echo ""
echo "Для просмотра логов:"
echo "tail -f storage/logs/laravel.log"
