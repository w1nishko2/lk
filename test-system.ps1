# Скрипт быстрой проверки системы email-кампаний для Windows
Write-Host "=== Проверка системы email-кампаний ===" -ForegroundColor Green

# Проверка конфигурации Laravel
Write-Host "1. Проверка конфигурации Laravel..." -ForegroundColor Yellow
php artisan config:show | Select-String -Pattern "(APP_ENV|DB_DATABASE|MAIL_MAILER|QUEUE_CONNECTION)"

Write-Host ""
Write-Host "2. Проверка подключения к базе данных..." -ForegroundColor Yellow
php artisan migrate:status

Write-Host ""
Write-Host "3. Проверка шаблонов email..." -ForegroundColor Yellow
php artisan tinker --execute="echo App\Models\EmailTemplate::count() . ' шаблонов найдено';"

Write-Host ""
Write-Host "4. Проверка очередей..." -ForegroundColor Yellow
php artisan email:queue-monitor

Write-Host ""
Write-Host "5. Проверка команды обработки запланированных кампаний..." -ForegroundColor Yellow
php artisan email:process-scheduled

Write-Host ""
Write-Host "6. Тест создания тестовой кампании..." -ForegroundColor Yellow
$testCommand = @"
`$campaign = new App\Models\EmailCampaign([
    'name' => 'Test Campaign',
    'subject' => 'Тестовое письмо', 
    'template_id' => 1,
    'status' => 'draft',
    'total_recipients' => 10,
    'batch_size' => 100,
    'delay_between_batches' => 60,
    'emails_per_minute' => 30
]);
`$campaign->save();
echo 'Тестовая кампания создана с ID: ' . `$campaign->id;
"@

php artisan tinker --execute=$testCommand

Write-Host ""
Write-Host "=== Проверка завершена ===" -ForegroundColor Green
Write-Host ""
Write-Host "Для запуска полного теста выполните:" -ForegroundColor Cyan
Write-Host "php artisan email:queue-monitor" -ForegroundColor White
Write-Host ""
Write-Host "Для просмотра логов:" -ForegroundColor Cyan
Write-Host "Get-Content storage/logs/laravel.log -Tail 20 -Wait" -ForegroundColor White
Write-Host ""
Write-Host "Для тестирования отправки:" -ForegroundColor Cyan
Write-Host "php artisan queue:work --once --queue=default,medium-email,bulk-email" -ForegroundColor White
