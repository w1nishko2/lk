@echo off
REM Скрипт для автоматической отправки email каждые 15 секунд
REM Запускайте этот файл, чтобы начать автоматическую рассылку

cd /d "C:\ospanel\domains\konstructor"

:loop
echo [%time%] Отправка пакета email...
php artisan email:send-mass --send --batch=5

REM Пауза 15 секунд
timeout /t 15 /nobreak > nul

goto loop
