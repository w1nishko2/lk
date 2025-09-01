<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subject ?? 'WeebS - Создаем продающие сайты'); ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .content p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .features {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .feature-icon {
            width: 20px;
            height: 20px;
            background-color: #667eea;
            border-radius: 50%;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: transform 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        
        .unsubscribe {
            margin-top: 20px;
            font-size: 12px;
            opacity: 0.6;
        }
        
        .unsubscribe a {
            color: #ecf0f1;
            text-decoration: underline;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 5px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content h2 {
                font-size: 20px;
            }
            
            .features {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1><?php echo e(config('app.name')); ?></h1>
            <p>Создаем продающие сайты легко и быстро</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>🚀 Запустите свой бизнес в интернете!</h2>
            
            <p>Привет!</p>
            
            <p>Хотите создать <strong>продающий сайт</strong>, который будет приносить вам клиентов и деньги? 
            Мы поможем вам сделать это быстро, легко и без технических знаний!</p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"></div>
                    <div>✅ <strong>Готовые шаблоны</strong> - выберите из множества профессиональных дизайнов</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"></div>
                    <div>✅ <strong>Простой конструктор</strong> - создавайте сайт без программирования</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"></div>
                    <div>✅ <strong>Мобильная адаптация</strong> - ваш сайт будет идеально выглядеть на всех устройствах</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"></div>
                    <div>✅ <strong>SEO-оптимизация</strong> - ваш сайт будут находить в поисковиках</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"></div>
                    <div>✅ <strong>Быстрый запуск</strong> - от идеи до готового сайта за несколько часов</div>
                </div>
            </div>
            
            <p><strong>🎁 Специальное предложение:</strong> Первые 100 клиентов получают скидку 50% на создание сайта!</p>
            
            <div style="text-align: center;">
                <a href="<?php echo e(config('app.url')); ?>" class="cta-button">
                    🚀 Создать сайт сейчас
                </a>
            </div>
            
            <p>Не упустите возможность выйти в онлайн и начать зарабатывать уже сегодня!</p>
            
            <p style="font-size: 14px; color: #999;">
                <em>P.S. Если у вас есть вопросы, просто ответьте на это письмо - мы всегда готовы помочь!</em>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                <a href="#">📱 Telegram</a>
                <a href="#">📧 Email</a>
                <a href="#">🌐 Сайт</a>
            </div>
            
            <p>© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Все права защищены.</p>
            <p><?php echo e(config('app.url')); ?></p>
            
            <div class="unsubscribe">
                <p>Если вы не хотите получать наши письма, <a href="#unsubscribe">отпишитесь здесь</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\konstructor\resources\views\emails\mass-email.blade.php ENDPATH**/ ?>