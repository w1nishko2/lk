@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Создание новой рассылки</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('email-campaigns.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Название рассылки <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Тема письма <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="template_id" class="form-label">Шаблон (необязательно)</label>
                            <select class="form-select" id="template_id" name="template_id">
                                <option value="">Выберите шаблон...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Содержание письма <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Поддерживается HTML разметка. Можете использовать переменные: @{{ '{{email}}' }} и @{{ '{{name}}' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_list" class="form-label">Файл со списком email адресов <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('email_list') is-invalid @enderror" 
                                   id="email_list" name="email_list" accept=".txt,.csv" required>
                            @error('email_list')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Поддерживаются форматы: .txt (по одному email на строку) и .csv
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Запланировать на время (необязательно)</label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" 
                                   min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Оставьте пустым для немедленной отправки
                            </div>
                        </div>

                        <!-- Настройки пакетной отправки -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <a class="text-decoration-none" data-bs-toggle="collapse" href="#batchSettings" role="button" aria-expanded="false">
                                        <i class="fas fa-cog me-2"></i>Расширенные настройки рассылки
                                    </a>
                                </h6>
                            </div>
                            <div class="collapse" id="batchSettings">
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Автоматическая оптимизация:</strong> Система автоматически настроит оптимальные параметры в зависимости от размера вашей базы. Эти настройки переопределят автоматические.
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="batch_size" class="form-label">Размер пакета</label>
                                                <input type="number" class="form-control @error('batch_size') is-invalid @enderror" 
                                                       id="batch_size" name="batch_size" value="{{ old('batch_size') }}" 
                                                       min="1" max="1000" placeholder="Авто">
                                                @error('batch_size')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    Количество писем в одном пакете (1-1000)
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="delay_between_batches" class="form-label">Задержка между пакетами (сек)</label>
                                                <input type="number" class="form-control @error('delay_between_batches') is-invalid @enderror" 
                                                       id="delay_between_batches" name="delay_between_batches" 
                                                       value="{{ old('delay_between_batches') }}" 
                                                       min="30" max="3600" placeholder="Авто">
                                                @error('delay_between_batches')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    Пауза между пакетами (30-3600 сек)
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="emails_per_minute" class="form-label">Писем в минуту</label>
                                                <input type="number" class="form-control @error('emails_per_minute') is-invalid @enderror" 
                                                       id="emails_per_minute" name="emails_per_minute" 
                                                       value="{{ old('emails_per_minute') }}" 
                                                       min="1" max="60" placeholder="Авто">
                                                @error('emails_per_minute')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    Скорость отправки (1-60 в мин)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Рекомендации:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Для баз 100k+ используйте пакеты по 50 писем с задержкой 300 сек</li>
                                            <li>Для баз 10k-50k используйте пакеты по 100 писем с задержкой 180 сек</li>
                                            <li>Не превышайте 60 писем в минуту для избежания блокировок</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('email-campaigns.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Создать рассылку
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Подгрузка содержимого шаблона
document.getElementById('template_id').addEventListener('change', function() {
    const templateId = this.value;
    if (templateId) {
        fetch(`/email-templates/${templateId}/content`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('subject').value = data.subject;
                document.getElementById('content').value = data.content;
            });
    }
});
</script>
@endpush
@endsection
