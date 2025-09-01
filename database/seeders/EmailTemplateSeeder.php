<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Современный корпоративный шаблон',
                'subject' => 'Важное сообщение от Weebs.ru',
                'content' => '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщение от Weebs.ru</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center;">
            <div style="font-size: 28px; font-weight: bold; margin-bottom: 10px;">Weebs.ru</div>
            <div style="font-size: 16px; opacity: 0.9;">Ваш надёжный партнёр</div>
        </div>
        
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px; font-size: 24px;">Здравствуйте!</h2>
            <p style="color: #555; line-height: 1.6; margin: 0 0 20px; font-size: 16px;">
                Мы рады приветствовать вас! У нас есть важная информация, которой хотим поделиться.
            </p>
            
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 8px; margin: 30px 0; color: white; text-align: center;">
                <h3 style="margin: 0 0 15px; font-size: 20px;">Специальное предложение!</h3>
                <p style="margin: 0 0 20px; opacity: 0.9;">Не упустите возможность воспользоваться нашими услугами</p>
                <a href="http://weebs.ru/kp/" style="display: inline-block; background: white; color: #f5576c; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    Узнать подробнее
                </a>
            </div>
            
            <p style="color: #555; line-height: 1.6; margin: 0 0 20px; font-size: 16px;">
                Посетите наш основной сайт для получения дополнительной информации и актуальных новостей.
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="http://weebs.ru/" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 16px;">
                    Перейти на Weebs.ru
                </a>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
            <p style="color: #6c757d; margin: 0 0 15px; font-size: 14px;">
                С уважением,<br><strong>Команда Weebs.ru</strong>
            </p>
            <div style="margin: 20px 0;">
                <a href="http://weebs.ru/" style="color: #667eea; text-decoration: none; margin: 0 10px;">Главная</a>
                <span style="color: #dee2e6;">|</span>
                <a href="http://weebs.ru/kp/" style="color: #667eea; text-decoration: none; margin: 0 10px;">Коммерческое предложение</a>
            </div>
            <p style="color: #adb5bd; margin: 15px 0 0; font-size: 12px;">
                Это письмо отправлено автоматически. Пожалуйста, не отвечайте на него.
            </p>
        </div>
    </div>
</body>
</html>',
                'variables' => ['email'],
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Промо акция Weebs.ru',
                'subject' => '🎉 Специальное предложение от Weebs.ru!',
                'content' => '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Специальное предложение</title>
</head>
<body style="margin: 0; padding: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: Arial, sans-serif; min-height: 100vh; padding: 20px 0;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
        <div style="background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; padding: 40px 30px; text-align: center; position: relative;">
            <div style="position: absolute; top: 10px; right: 20px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                СРОЧНО!
            </div>
            <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
            <h1 style="margin: 0 0 10px; font-size: 28px; font-weight: bold;">
                СПЕЦИАЛЬНОЕ ПРЕДЛОЖЕНИЕ!
            </h1>
            <p style="margin: 0; font-size: 16px; opacity: 0.9;">
                Эксклюзивное предложение от Weebs.ru
            </p>
        </div>
        
        <div style="padding: 40px 30px;">
            <div style="background: linear-gradient(135deg, #ffeef8 0%, #f0e6ff 100%); border: 2px solid #ff416c; border-radius: 16px; padding: 30px; margin-bottom: 30px; text-align: center; position: relative;">
                <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #ff416c; color: white; padding: 5px 20px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                    ⏰ ОГРАНИЧЕННОЕ ВРЕМЯ
                </div>
                <div style="margin-top: 10px;">
                    <h2 style="color: #ff416c; margin: 0 0 20px; font-size: 24px;">
                        Не упустите шанс!
                    </h2>
                    <p style="color: #333; line-height: 1.6; margin: 0 0 25px; font-size: 18px;">
                        Уникальная возможность воспользоваться нашими премиум-услугами по специальной цене!
                    </p>
                    
                    <div style="background: white; padding: 20px; border-radius: 12px; margin: 20px 0;">
                        <div style="font-size: 14px; color: #666; margin-bottom: 10px;">💝 Ваша выгода</div>
                        <div style="font-size: 36px; color: #ff416c; font-weight: bold; margin: 10px 0;">до 50%</div>
                        <div style="color: #333; font-size: 16px;">скидка на все услуги</div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin: 40px 0;">
                <p style="color: #555; line-height: 1.6; margin: 0 0 30px; font-size: 16px;">
                    Ознакомьтесь с полным коммерческим предложением и выберите подходящий для вас пакет услуг.
                </p>
                
                <div style="margin: 30px 0;">
                    <a href="http://weebs.ru/kp/" style="display: inline-block; background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; padding: 18px 40px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 18px; margin: 0 10px 10px;">
                        📋 Смотреть предложение
                    </a>
                </div>
                
                <p style="color: #666; margin: 30px 0 0; font-size: 14px;">
                    Или посетите наш основной сайт:
                </p>
                <a href="http://weebs.ru/" style="display: inline-block; color: #667eea; text-decoration: none; font-weight: bold; padding: 10px 20px; border: 2px solid #667eea; border-radius: 8px; margin-top: 10px;">
                    🌐 Weebs.ru
                </a>
            </div>
        </div>
        
        <div style="background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #667eea;">Weebs.ru</div>
            <p style="color: #6c757d; margin: 0 0 15px; font-size: 14px;">
                Ваш надёжный партнёр в мире цифровых решений
            </p>
            <div style="margin: 20px 0;">
                <a href="http://weebs.ru/" style="color: #667eea; text-decoration: none; margin: 0 15px; font-size: 14px;">Главная</a>
                <a href="http://weebs.ru/kp/" style="color: #667eea; text-decoration: none; margin: 0 15px; font-size: 14px;">Коммерческое предложение</a>
            </div>
            <p style="color: #adb5bd; margin: 15px 0 0; font-size: 12px;">
                © 2025 Weebs.ru. Все права защищены.<br>
                Это автоматическое письмо. Не отвечайте на него.
            </p>
        </div>
    </div>
</body>
</html>',
                'variables' => ['email'],
                'is_active' => true,
                'user_id' => 1,
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
