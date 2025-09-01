<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предпросмотр: {{ $emailCampaign->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .preview-header {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .preview-content {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .back-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="preview-header">
        <a href="{{ route('email-campaigns.show', $emailCampaign) }}" class="back-link">
            ← Вернуться к рассылке
        </a>
        <h1>{{ $emailCampaign->name }}</h1>
        <p><strong>Тема:</strong> {{ $emailCampaign->subject }}</p>
        <p><strong>Шаблон:</strong> {{ ucfirst($emailCampaign->template) }}</p>
    </div>

    <div class="preview-content">
        {!! $content !!}
    </div>
</body>
</html>
