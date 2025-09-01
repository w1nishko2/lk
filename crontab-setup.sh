#!/bin/bash
# Crontab configuration for Email Campaign System
# Add these lines to your crontab using: crontab -e

# ===========================================
# EMAIL QUEUE WORKERS - Основные обработчики очередей
# ===========================================

# Запуск основного воркера очереди каждую минуту (автоперезапуск)
* * * * * cd lk_weebs_ru && php artisan queue:work database --queue=default --tries=3 --timeout=300 --memory=128 --sleep=3 --max-jobs=100 --stop-when-empty > /dev/null 2>&1

# Запуск воркера для средних рассылок каждые 2 минуты
*/2 * * * * cd lk_weebs_ru && php artisan queue:work database --queue=medium-email --tries=3 --timeout=1800 --memory=256 --sleep=5 --max-jobs=50 --stop-when-empty > /dev/null 2>&1

# Запуск воркера для больших рассылок каждые 5 минут
*/5 * * * * cd lk_weebs_ru && php artisan queue:work database --queue=bulk-email --tries=2 --timeout=3600 --memory=512 --sleep=10 --max-jobs=20 --stop-when-empty > /dev/null 2>&1

# ===========================================
# МОНИТОРИНГ И ОБСЛУЖИВАНИЕ
# ===========================================

# Мониторинг статуса кампаний каждые 5 минут
*/5 * * * * cd lk_weebs_ru && php artisan email:monitor status >> lk_weebs_ru/storage/logs/email-monitor.log 2>&1

# Очистка завершенных задач каждый час
0 * * * * cd lk_weebs_ru && php artisan queue:prune-completed --hours=24 > /dev/null 2>&1

# Перезапуск зависших кампаний каждые 30 минут
*/30 * * * * cd lk_weebs_ru && php artisan queue:retry all > /dev/null 2>&1

# ===========================================
# ЗАПЛАНИРОВАННЫЕ РАССЫЛКИ
# ===========================================

# Проверка и запуск запланированных кампаний каждую минуту
* * * * * cd lk_weebs_ru && php artisan schedule:run > /dev/null 2>&1

# ===========================================
# ОЧИСТКА И ОБСЛУЖИВАНИЕ СИСТЕМЫ
# ===========================================

# Ротация логов каждый день в 2:00
0 2 * * * cd lk_weebs_ru && php artisan logs:clear --days=7 > /dev/null 2>&1

# Очистка временных файлов каждый день в 3:00
0 3 * * * cd lk_weebs_ru && find lk_weebs_ru/storage/app/temp -type f -mtime +1 -delete > /dev/null 2>&1

# Backup базы данных каждый день в 4:00
0 4 * * * cd lk_weebs_ru && php artisan db:backup > /dev/null 2>&1

# ===========================================
# ДОПОЛНИТЕЛЬНЫЕ КОМАНДЫ ДЛЯ УПРАВЛЕНИЯ
# ===========================================

# Команды для ручного управления (закомментированы):
# cd lk_weebs_ru && php artisan email:monitor pause --campaign=ID
# cd lk_weebs_ru && php artisan email:monitor resume --campaign=ID
# cd lk_weebs_ru && php artisan email:monitor stats

# ===========================================
# ВАЖНЫЕ ЗАМЕЧАНИЯ:
# ===========================================
# 1. Замените lk_weebs_ru на реальный путь к вашему проекту
# 2. Убедитесь, что путь к PHP корректный (обычно это просто 'php' на shared hosting)
# 3. Для больших баз (500k+) рекомендуется увеличить лимиты памяти
# 4. Логи будут сохранены в storage/logs/
# 5. При возникновении проблем проверьте права на файлы (chmod 755)
