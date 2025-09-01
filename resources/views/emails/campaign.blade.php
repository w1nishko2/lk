<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $campaign->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .button:hover {
            background-color: #0056b3;
        }
        
        h1, h2, h3 {
            color: #2c3e50;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .unsubscribe {
            font-size: 11px;
            color: #999;
            text-align: center;
            margin-top: 20px;
        }
        
        .unsubscribe a {
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>
    
    <div class="content">
        {!! $campaign->content !!}
    </div>
    
    <div class="footer">
        <p>Это письмо отправлено автоматически системой {{ config('app.name') }}</p>
        <div class="unsubscribe">
            <p>
                <a href="#" style="color: #999;">Отписаться от рассылки</a>
            </p>
        </div>
    </div>
</body>
</html>
