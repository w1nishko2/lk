<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subject ?? 'WeebS - Профессиональная веб-разработка'); ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
            color: #212529;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 2px solid #000000;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .header {
            background-color: #000000;
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            border-bottom: 4px solid #000000;
        }
        
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 15px 0 0 0;
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        
        .content h2 {
            color: #000000;
            font-size: 28px;
            margin-bottom: 25px;
            border-bottom: 3px solid #000000;
            padding-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .content p {
            color: #333333;
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        
        .highlight-box {
            background-color: #000000;
            color: #ffffff;
            padding: 25px;
            margin: 30px 0;
            border-left: 6px solid #666666;
        }
        
        .highlight-box h3 {
            margin: 0 0 15px 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .services-grid {
            background-color: #f8f9fa;
            padding: 30px;
            margin: 30px 0;
            border: 2px solid #000000;
        }
        
        .service-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #000000;
        }
        
        .service-icon {
            width: 8px;
            height: 8px;
            background-color: #000000;
            margin-right: 20px;
            flex-shrink: 0;
            transform: rotate(45deg);
        }
        
        .service-item strong {
            color: #000000;
        }
        
        .cta-section {
            text-align: center;
            padding: 30px;
            background-color: #000000;
            color: #ffffff;
            margin: 30px 0;
        }
        
        .cta-button {
            display: inline-block;
            background-color: #ffffff;
            color: #000000;
            text-decoration: none;
            padding: 18px 40px;
            border: 3px solid #000000;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background-color: #000000;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .cta-button.secondary {
            background-color: transparent;
            color: #ffffff;
            border-color: #ffffff;
        }
        
        .cta-button.secondary:hover {
            background-color: #ffffff;
            color: #000000;
        }
        
        .stats-section {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            background-color: #f8f9fa;
            padding: 25px;
            border: 2px solid #000000;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #000000;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        
        .footer {
            background-color: #000000;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        
        .footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }
        
        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 15px;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ffffff;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: #ffffff;
            color: #000000;
        }
        
        .unsubscribe {
            margin-top: 25px;
            font-size: 12px;
            opacity: 0.6;
        }
        
        .unsubscribe a {
            color: #cccccc;
            text-decoration: underline;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-width: 1px;
            }
            
            .header, .content, .footer, .cta-section {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content h2 {
                font-size: 22px;
            }
            
            .services-grid {
                padding: 20px;
            }
            
            .stats-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .cta-button {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>WeebS</h1>
            <p>Профессиональная веб-разработка</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>🚀 Превратите идею в прибыльный бизнес</h2>
            
            <p><strong>Здравствуйте!</strong></p>
            
            <p>Нужен <strong>профессиональный веб-сайт</strong>, который будет работать на вас 24/7 и приносить реальную прибыль? 
            Мы создаем сайты, которые не просто красиво выглядят, а <strong>продают ваши товары и услуги</strong>.</p>
            
            <!-- Highlight Box -->
            <div class="highlight-box">
                <h3>⚡ ПОЧЕМУ ВЫБИРАЮТ НАС?</h3>
                <p>За 5+ лет работы мы создали более 200 успешных проектов. Наши сайты увеличивают конверсию в среднем на 40% и окупаются за первые 3 месяца.</p>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stat-item">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Проектов</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">5+</span>
                    <span class="stat-label">Лет опыта</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">40%</span>
                    <span class="stat-label">Рост конверсии</span>
                </div>
            </div>
            
            <!-- Services -->
            <div class="services-grid">
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>Корпоративные сайты</strong> - представительские сайты для серьезного бизнеса</div>
                </div>
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>Интернет-магазины</strong> - продающие площадки с полным функционалом</div>
                </div>
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>Landing Page</strong> - одностраничники с высокой конверсией</div>
                </div>
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>Веб-приложения</strong> - сложные системы под ваши задачи</div>
                </div>
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>SEO и продвижение</strong> - вывод в ТОП поисковых систем</div>
                </div>
                <div class="service-item">
                    <div class="service-icon"></div>
                    <div><strong>Техподдержка 24/7</strong> - ваш сайт всегда в рабочем состоянии</div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="cta-section">
                <h3 style="margin-bottom: 20px; font-size: 24px;">🎁 СПЕЦИАЛЬНОЕ ПРЕДЛОЖЕНИЕ</h3>
                <p style="margin-bottom: 25px; font-size: 18px;">Первым 50 клиентам - скидка 30% на разработку + бесплатная консультация</p>
                
                <a href="https://weebs.ru" class="cta-button">
                    Посмотреть портфолио
                </a>
                <a href="https://weebs.ru/kp" class="cta-button secondary">
                    Получить коммерческое предложение
                </a>
            </div>
            
            <p><strong>💼 Что вы получаете:</strong></p>
            <ul style="margin-left: 20px; color: #333333;">
                <li>Уникальный дизайн под ваш бренд</li>
                <li>Адаптивную верстку для всех устройств</li>
                <li>Быструю загрузку и SEO-оптимизацию</li>
                <li>Систему управления контентом</li>
                <li>Интеграцию с CRM и аналитикой</li>
                <li>Гарантию 12 месяцев</li>
            </ul>
            
            <p style="font-size: 14px; color: #666666; margin-top: 30px;">
                <em>P.S. Не откладывайте запуск вашего бизнеса в интернете. Каждый день промедления - это потерянные клиенты и прибыль. Ответьте на это письмо, и мы обсудим ваш проект уже сегодня!</em>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                <a href="https://weebs.ru">Портфолио</a>
                <a href="https://weebs.ru/kp">Коммерческое предложение</a>
                <a href="mailto:w1nishko2@yandex.ru">Связаться с нами</a>
            </div>
            
            <p>© <?php echo e(date('Y')); ?> WeebS - Профессиональная веб-разработка</p>
            <p><strong>https://weebs.ru</strong> | Email: w1nishko2@yandex.ru</p>
            
            <div class="unsubscribe">
                <p>Если вы не хотите получать наши предложения, <a href="#unsubscribe">отпишитесь здесь</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\OSPanel\domains\konstructor\resources\views/emails/mass-email.blade.php ENDPATH**/ ?>