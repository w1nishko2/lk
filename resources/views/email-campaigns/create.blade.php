@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('email-campaigns.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left me-1"></i>
                    Назад
                </a>
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    Создать рассылку
                </h1>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        Настройка новой email рассылки
                    </h5>
                </div>
                
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-1"></i> Пожалуйста, исправьте следующие ошибки:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('email-campaigns.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag text-primary me-1"></i>
                                        Название кампании *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Например: Акция по продаже конструктора сайтов"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template" class="form-label">
                                        <i class="fas fa-palette text-info me-1"></i>
                                        Шаблон письма *
                                    </label>
                                    <select class="form-select @error('template') is-invalid @enderror" 
                                            id="template" 
                                            name="template" 
                                            required>
                                        @foreach($templates as $key => $name)
                                            <option value="{{ $key }}" {{ old('template') == $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('template')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delay_seconds" class="form-label">
                                        <i class="fas fa-clock text-warning me-1"></i>
                                        Задержка между отправками (сек) *
                                    </label>
                                    <select class="form-select @error('delay_seconds') is-invalid @enderror" 
                                            id="delay_seconds" 
                                            name="delay_seconds" 
                                            required>
                                        <option value="30" {{ old('delay_seconds', 30) == 30 ? 'selected' : '' }}>30 секунд (рекомендуется)</option>
                                        <option value="60" {{ old('delay_seconds') == 60 ? 'selected' : '' }}>1 минута</option>
                                        <option value="120" {{ old('delay_seconds') == 120 ? 'selected' : '' }}>2 минуты</option>
                                        <option value="300" {{ old('delay_seconds') == 300 ? 'selected' : '' }}>5 минут</option>
                                        <option value="600" {{ old('delay_seconds') == 600 ? 'selected' : '' }}>10 минут</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-shield-alt text-success me-1"></i>
                                        Задержка помогает избежать блокировки как спам
                                    </div>
                                    @error('delay_seconds')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <i class="fas fa-heading text-primary me-1"></i>
                                Тема письма *
                            </label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   placeholder="🚀 Создайте сайт за 15 минут со скидкой 50%!"
                                   required>
                            <div class="form-text">
                                Используйте эмодзи и призывы к действию для лучшей открываемости
                            </div>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">
                                <i class="fas fa-edit text-success me-1"></i>
                                Содержание письма *
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="10"
                                      placeholder="Напишите содержание вашего письма здесь. Используйте {{name}} для вставки имени получателя."
                                      required>{{ old('content', 'Устали от дорогих веб-студий и сложных решений?

Представляем вашему вниманию революционный конструктор сайтов!

🎯 Что вы получите:
• Готовые блоки для любого бизнеса
• Адаптивный дизайн под все устройства  
• Быстрое создание - от идеи до готового сайта за 15 минут
• Профессиональный результат без технических знаний

💰 Экономия времени и денег:
Вместо месяцев ожидания и десятков тысяч рублей - получите готовый сайт уже сегодня!

🔥 Ограниченное предложение:
Скидка 50% только для первых 100 клиентов!

Не упустите шанс вывести свой бизнес на новый уровень!') }}</textarea>
                            <div class="form-text">
                                <strong>Доступные переменные:</strong> {{name}} - имя получателя, {{email}} - email получателя
                            </div>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="recipients_file" class="form-label">
                                <i class="fas fa-file-upload text-info me-1"></i>
                                Файл с получателями *
                            </label>
                            <input type="file" 
                                   class="form-control @error('recipients_file') is-invalid @enderror" 
                                   id="recipients_file" 
                                   name="recipients_file" 
                                   accept=".txt"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Загрузите .txt файл с email адресами (по одному на строку). Максимум 10 МБ.
                            </div>
                            @error('recipients_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-shield-alt me-1"></i> Важные рекомендации для избежания спама:</h6>
                            <ul class="mb-0">
                                <li><strong>Задержка:</strong> Устанавливайте достаточную задержку между отправками</li>
                                <li><strong>Качество базы:</strong> Используйте только валидные email адреса</li>
                                <li><strong>Содержание:</strong> Избегайте слов-триггеров спама ("бесплатно", "срочно", много восклицательных знаков)</li>
                                <li><strong>Репутация:</strong> Начинайте с малых объемов для прогрева почты</li>
                                <li><strong>Отписка:</strong> Всегда предоставляйте возможность отписаться</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('email-campaigns.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Отмена
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>
                                Создать рассылку
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Предпросмотр содержания
    const contentTextarea = document.getElementById('content');
    const templateSelect = document.getElementById('template');
    
    // Подсчет символов
    const maxLength = 5000;
    const charCount = document.createElement('small');
    charCount.className = 'text-muted';
    contentTextarea.parentNode.appendChild(charCount);
    
    function updateCharCount() {
        const remaining = maxLength - contentTextarea.value.length;
        charCount.textContent = `Осталось символов: ${remaining}`;
        charCount.className = remaining < 100 ? 'text-danger' : 'text-muted';
    }
    
    contentTextarea.addEventListener('input', updateCharCount);
    updateCharCount();
    
    // Валидация файла
    const fileInput = document.getElementById('recipients_file');
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) { // 10 MB
                alert('Файл слишком большой. Максимальный размер: 10 МБ');
                e.target.value = '';
                return;
            }
            
            if (!file.name.toLowerCase().endsWith('.txt')) {
                alert('Пожалуйста, выберите файл с расширением .txt');
                e.target.value = '';
                return;
            }
        }
    });
});
</script>
@endpush
