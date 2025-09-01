#!/bin/bash

# Скрипт для автоматической отправки email каждые 15 секунд
# Для использования с crontab добавьте эти строки:
#
# * * * * * /path/to/konstructor/send-email-batch.sh
# * * * * * sleep 15 && /path/to/konstructor/send-email-batch.sh  
# * * * * * sleep 30 && /path/to/konstructor/send-email-batch.sh
# * * * * * sleep 45 && /path/to/konstructor/send-email-batch.sh

cd "$(dirname "$0")"

# Логирование
echo "[$(date)] Отправка пакета email..." >> email-sender.log

# Отправка email
php artisan email:send-mass --send --batch=5 >> email-sender.log 2>&1

echo "[$(date)] Пакет обработан" >> email-sender.log
