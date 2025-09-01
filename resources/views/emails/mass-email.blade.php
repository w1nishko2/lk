<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создадим сайт, который продает</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333333;
            line-height: 1.5;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #000000;
            padding: 0;
        }
        
        .header {
            background: #000000;
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 2px;
        }
        
        .content {
            padding: 40px;
        }
        
        .headline {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            margin: 0 0 20px 0;
            text-align: center;
        }
        
        .subheadline {
            font-size: 18px;
            color: #333333;
            margin: 0 0 30px 0;
            text-align: center;
        }
        
        .problem {
            background: #f5f5f5;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #000000;
        }
        
        .problem p {
            margin: 0;
            font-size: 16px;
            color: #333333;
        }
        
        .solution {
            margin: 30px 0;
        }
        
        .benefit {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .benefit-icon {
            width: 6px;
            height: 6px;
            background: #000000;
            margin: 8px 15px 0 0;
            flex-shrink: 0;
        }
        
        .benefit-text {
            font-size: 16px;
            color: #333333;
        }
        
        .cta-section {
            background: #000000;
            color: #ffffff;
            padding: 40px;
            text-align: center;
            margin: 30px 0;
        }
        
        .cta-headline {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 20px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: #ffffff;
            color: #000000;
            text-decoration: none;
            padding: 15px 30px;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #000000;
            margin: 15px 10px;
            transition: all 0.3s;
        }
        
        .cta-button:hover {
            background: #000000;
            color: #ffffff;
            border-color: #ffffff;
        }
        
        .urgency {
            background: #ffffff;
            border: 2px solid #000000;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .urgency-text {
            font-size: 14px;
            color: #333333;
            margin: 0;
        }
        
        .footer {
            background: #f5f5f5;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #000000;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #666666;
        }
        
        .footer a {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
        }
        
        .unsubscribe {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
        }
        
        .unsubscribe a {
            color: #999999;
            text-decoration: underline;
        }
        
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .container {
                border-width: 1px;
            }
            
            .header, .content, .cta-section, .footer {
                padding: 20px;
            }
            
            .headline {
                font-size: 20px;
            }
            
            .subheadline {
                font-size: 16px;
            }
            
            .cta-button {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="logo">WeebS</h1>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2 class="headline">Ваш сайт не приносит клиентов?</h2>
            <p class="subheadline">Мы создаем сайты, которые реально продают</p>
            
            <!-- Problem -->
            <div class="problem">
                <p><strong>Проблема:</strong> У вас либо нет сайта, либо он не работает на результат. Клиенты не находят вас в интернете, а конкуренты уводят ваших покупателей.</p>
            </div>
            
            <!-- Solution -->
            <div class="solution">
                <p><strong>Решение:</strong> Профессиональный сайт от WeebS</p>
                
                <div class="benefit">
                    <div class="benefit-icon"></div>
                    <div class="benefit-text"><strong>Привлекаем клиентов</strong> - SEO-оптимизация выведет вас в ТОП поиска</div>
                </div>
                
                <div class="benefit">
                    <div class="benefit-icon"></div>
                    <div class="benefit-text"><strong>Увеличиваем продажи</strong> - продающие тексты и призывы к действию</div>
                </div>
                
                <div class="benefit">
                    <div class="benefit-icon"></div>
                    <div class="benefit-text"><strong>Экономим ваше время</strong> - полный цикл от идеи до запуска</div>
                </div>
                
                <div class="benefit">
                    <div class="benefit-icon"></div>
                    <div class="benefit-text"><strong>Гарантируем результат</strong> - 12 месяцев техподдержки включены</div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="cta-section">
                <h3 class="cta-headline">ПОЛУЧИТЕ БЕСПЛАТНУЮ КОНСУЛЬТАЦИЮ СЕГОДНЯ</h3>
                <p style="margin: 0 0 20px 0;">Обсудим ваш проект и покажем примеры наших работ</p>
                
                <a href="https://weebs.ru/kp" class="cta-button">ПОЛУЧИТЬ ПРЕДЛОЖЕНИЕ</a>
                <a href="https://weebs.ru" class="cta-button">СМОТРЕТЬ ПОРТФОЛИО</a>
            </div>
            
            <!-- Urgency -->
            <div class="urgency">
                <p class="urgency-text">⚡ <strong>ТОЛЬКО В СЕНТЯБРЕ:</strong> Скидка 25% на разработку + домен в подарок</p>
            </div>
            
            <p style="font-size: 14px; color: #666666; text-align: center; margin-top: 30px;">
                <em>P.S. Каждый день без сайта = потерянная прибыль. Не откладывайте — ответьте на письмо или перейдите по ссылке выше.</em>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>WeebS</strong> | Веб-разработка с гарантией результата</p>
            <p>📧 <a href="mailto:w1nishko2@yandex.ru">w1nishko2@yandex.ru</a> | 🌐 <a href="https://weebs.ru">weebs.ru</a></p>
            
            <div class="unsubscribe">
                <p>Не хотите получать наши предложения? <a href="#unsubscribe">Отписаться</a></p>
            </div>
        </div>
    </div>
</body>
</html>
