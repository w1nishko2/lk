# Руководство по развертыванию системы email-кампаний

## Подготовка к развертыванию на sweb.ru

### 1. Загрузка файлов
Загрузите все файлы проекта в папку `lk_weebs_ru` на вашем хостинге.

### 2. Настройка окружения
Создайте файл `.env` на основе `.env.example`:

```bash
cd lk_weebs_ru
cp .env.example .env
```

### 3. Настройка базы данных в .env
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ваша_база_данных
DB_USERNAME=ваш_пользователь
DB_PASSWORD=ваш_пароль
```

### 4. Настройка email в .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yandex.ru
MAIL_PORT=465
MAIL_USERNAME=your-email@yandex.ru
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@yandex.ru
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Настройка очередей
```env
QUEUE_CONNECTION=database
```

### 6. Выполнение команд установки

```bash
cd lk_weebs_ru

# Установка зависимостей
composer install --optimize-autoloader --no-dev

# Генерация ключа приложения
php8.1 artisan key:generate

# Выполнение миграций
php8.1 artisan migrate --force

# Заполнение шаблонов email
php8.1 artisan db:seed --class=EmailTemplateSeeder

# Создание символической ссылки для storage
php8.1 artisan storage:link

# Кэширование конфигурации для производительности
php8.1 artisan config:cache
php8.1 artisan route:cache
php8.1 artisan view:cache
```

### 7. Настройка Cron Jobs

Добавьте в crontab следующие задания (через панель управления sweb.ru или SSH):

```bash
# Основной планировщик Laravel
* * * * * cd lk_weebs_ru && php8.1 artisan schedule:run >> /dev/null 2>&1

# Обработка запланированных кампаний (каждые 5 минут)
*/5 * * * * cd lk_weebs_ru && php8.1 artisan email:process-scheduled >> storage/logs/scheduled-campaigns.log 2>&1

# Воркер очереди для крупных кампаний (постоянно работающий)
* * * * * cd lk_weebs_ru && php8.1 artisan queue:work --queue=bulk-email --tries=3 --timeout=300 --sleep=3 >> storage/logs/bulk-queue.log 2>&1

# Воркер очереди для средних кампаний
* * * * * cd lk_weebs_ru && php8.1 artisan queue:work --queue=medium-email --tries=3 --timeout=120 --sleep=1 >> storage/logs/medium-queue.log 2>&1

# Воркер очереди по умолчанию
* * * * * cd lk_weebs_ru && php8.1 artisan queue:work --queue=default --tries=3 --timeout=60 >> storage/logs/default-queue.log 2>&1

# Мониторинг очередей (каждые 15 минут)
*/15 * * * * cd lk_weebs_ru && php8.1 artisan email:queue-monitor >> storage/logs/queue-monitor.log 2>&1

# Очистка неудачных заданий (раз в день)
0 2 * * * cd lk_weebs_ru && php8.1 artisan queue:prune-failed --hours=24 >> /dev/null 2>&1

# Очистка старых логов (раз в неделю)
0 3 * * 0 cd lk_weebs_ru && find storage/logs -name "*.log" -mtime +7 -delete
```

## Использование системы

### Создание новой email-кампании

1. Перейдите в админ-панель
2. Создайте новую кампанию
3. Выберите шаблон из предустановленных (с брендингом Weebs.ru)
4. Загрузите базу email-адресов
5. Настройте параметры batch-отправки

### Автоматическая оптимизация

Система автоматически выберет оптимальные настройки в зависимости от размера базы:

- **До 1,000 писем**: обычная очередь, быстрая отправка
- **1,000 - 10,000 писем**: средняя очередь, умеренная скорость
- **Свыше 10,000 писем**: bulk очередь, медленная отправка

### Мониторинг

Для проверки статуса очередей используйте:

```bash
php8.1 artisan email:queue-monitor
```

### Логи

Все логи находятся в папке `storage/logs/`:
- `laravel.log` - основные логи приложения
- `bulk-queue.log` - логи обработки крупных кампаний
- `medium-queue.log` - логи средних кампаний
- `default-queue.log` - логи обычных задач
- `queue-monitor.log` - логи мониторинга

## Важные особенности

### Шаблоны email
Система включает 3 стильных шаблона с:
- Градиентным дизайном
- Адаптивной версткой
- Интеграцией с брендингом Weebs.ru
- Обязательными ссылками на http://weebs.ru/kp/ и http://weebs.ru/

### Batch-обработка
- Автоматическое разделение больших кампаний на батчи
- Контроль скорости отправки для избежания блокировок
- Возможность настройки интервалов между батчами

### Отказоустойчивость
- Автоматические повторы неудачных отправок
- Логирование всех операций
- Мониторинг состояния очередей

## Решение проблем

### Если письма не отправляются
1. Проверьте логи в `storage/logs/`
2. Убедитесь, что работают cron jobs
3. Проверьте настройки SMTP в `.env`

### Если очереди переполняются
1. Увеличьте количество воркеров в cron
2. Настройте больше интервалов между batch-отправками
3. Используйте команду мониторинга для диагностики

### Для технической поддержки
Обратитесь к документации Laravel или проверьте логи системы для подробной диагностики проблем.
