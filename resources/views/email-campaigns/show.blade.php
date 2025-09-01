@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('email-campaigns.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left me-1"></i>
                    К списку рассылок
                </a>
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    {{ $emailCampaign->name }}
                </h1>
            </div>

            <!-- Статус и основная информация -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ number_format($stats['total']) }}</h5>
                            <p class="card-text text-muted">Всего получателей</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title text-success">{{ number_format($stats['sent']) }}</h5>
                            <p class="card-text text-muted">Отправлено</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <h5 class="card-title text-warning">{{ number_format($stats['pending']) }}</h5>
                            <p class="card-text text-muted">В очереди</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h5 class="card-title text-danger">{{ number_format($stats['failed']) }}</h5>
                            <p class="card-text text-muted">Ошибки</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Прогресс -->
            @if($stats['total'] > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-info me-2"></i>
                            Прогресс отправки
                        </h5>
                        <span class="badge bg-primary fs-6">{{ number_format($emailCampaign->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $emailCampaign->progress_percentage }}%">
                            {{ number_format($stats['sent']) }} / {{ number_format($stats['total']) }}
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <small class="text-success">✓ Успешно: {{ number_format($stats['sent']) }}</small>
                        </div>
                        <div class="col">
                            <small class="text-warning">⏳ В очереди: {{ number_format($stats['pending']) }}</small>
                        </div>
                        <div class="col">
                            <small class="text-danger">✗ Ошибки: {{ number_format($stats['failed']) }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <!-- Детали кампании -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                Информация о рассылке
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Статус:</strong></td>
                                    <td>
                                        @switch($emailCampaign->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Черновик</span>
                                                @break
                                            @case('sending')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                                    Отправляется
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Завершена</span>
                                                @break
                                            @case('paused')
                                                <span class="badge bg-warning">Приостановлена</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Тема:</strong></td>
                                    <td>{{ $emailCampaign->subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Шаблон:</strong></td>
                                    <td>{{ ucfirst($emailCampaign->template) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Задержка:</strong></td>
                                    <td>{{ $emailCampaign->delay_seconds }} сек</td>
                                </tr>
                                <tr>
                                    <td><strong>Создана:</strong></td>
                                    <td>{{ $emailCampaign->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @if($emailCampaign->started_at)
                                <tr>
                                    <td><strong>Запущена:</strong></td>
                                    <td>{{ $emailCampaign->started_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($emailCampaign->completed_at)
                                <tr>
                                    <td><strong>Завершена:</strong></td>
                                    <td>{{ $emailCampaign->completed_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Управление -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs text-success me-2"></i>
                                Управление
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($emailCampaign->canStart())
                                    <button type="button" 
                                            class="btn btn-success btn-lg start-campaign" 
                                            data-campaign-id="{{ $emailCampaign->id }}">
                                        <i class="fas fa-play me-2"></i>
                                        Запустить рассылку
                                    </button>
                                @endif
                                
                                @if($emailCampaign->isSending())
                                    <button type="button" 
                                            class="btn btn-warning btn-lg pause-campaign" 
                                            data-campaign-id="{{ $emailCampaign->id }}">
                                        <i class="fas fa-pause me-2"></i>
                                        Приостановить рассылку
                                    </button>
                                @endif
                                
                                <a href="{{ route('email-campaigns.preview', $emailCampaign) }}" 
                                   class="btn btn-info" 
                                   target="_blank">
                                    <i class="fas fa-eye me-2"></i>
                                    Предпросмотр письма
                                </a>
                                
                                @if($stats['failed'] > 0)
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#errorsModal">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Посмотреть ошибки ({{ $stats['failed'] }})
                                    </button>
                                @endif
                            </div>
                            
                            @if($emailCampaign->isSending())
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Рассылка выполняется в фоне.</strong><br>
                                    Страница обновляется автоматически каждые 10 секунд.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Список получателей -->
            @if($emailCampaign->recipients->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        Получатели (показано последние 100)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Email</th>
                                    <th>Статус</th>
                                    <th>Отправлено</th>
                                    <th>Попытки</th>
                                    <th>Ошибка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emailCampaign->recipients->take(100) as $recipient)
                                    <tr>
                                        <td>
                                            <small class="font-monospace">{{ $recipient->email }}</small>
                                        </td>
                                        <td>
                                            @switch($recipient->status)
                                                @case('sent')
                                                    <span class="badge bg-success">Отправлено</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">Ошибка</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning">В очереди</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <small>
                                                {{ $recipient->sent_at ? $recipient->sent_at->format('d.m.Y H:i') : '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $recipient->attempts }}</span>
                                        </td>
                                        <td>
                                            @if($recipient->error_message)
                                                <small class="text-danger" title="{{ $recipient->error_message }}">
                                                    {{ Str::limit($recipient->error_message, 50) }}
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($emailCampaign->recipients->count() > 100)
                        <div class="text-center text-muted">
                            <small>... и еще {{ number_format($emailCampaign->recipients->count() - 100) }} получателей</small>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно с ошибками -->
@if($stats['failed'] > 0)
<div class="modal fade" id="errorsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Ошибки отправки
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Ошибка</th>
                                <th>Попытки</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emailCampaign->failedRecipients as $recipient)
                                <tr>
                                    <td><small class="font-monospace">{{ $recipient->email }}</small></td>
                                    <td><small class="text-danger">{{ $recipient->error_message }}</small></td>
                                    <td><span class="badge bg-secondary">{{ $recipient->attempts }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Запуск кампании
    const startButton = document.querySelector('.start-campaign');
    if (startButton) {
        startButton.addEventListener('click', function() {
            const campaignId = this.dataset.campaignId;
            
            if (confirm('Запустить рассылку? Это действие нельзя будет отменить.')) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Запускается...';
                
                fetch(`/email-campaigns/${campaignId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-play me-2"></i>Запустить рассылку';
                    }
                })
                .catch(error => {
                    alert('Произошла ошибка при запуске рассылки');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-play me-2"></i>Запустить рассылку';
                });
            }
        });
    }

    // Приостановка кампании
    const pauseButton = document.querySelector('.pause-campaign');
    if (pauseButton) {
        pauseButton.addEventListener('click', function() {
            const campaignId = this.dataset.campaignId;
            
            if (confirm('Приостановить рассылку?')) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Приостанавливается...';
                
                fetch(`/email-campaigns/${campaignId}/pause`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-pause me-2"></i>Приостановить рассылку';
                    }
                })
                .catch(error => {
                    alert('Произошла ошибка при приостановке рассылки');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-pause me-2"></i>Приостановить рассылку';
                });
            }
        });
    }
    
    // Автообновление для активных рассылок
    @if($emailCampaign->isSending())
    const autoRefreshInterval = setInterval(() => {
        location.reload();
    }, 10000); // Обновляем каждые 10 секунд
    
    // Останавливаем автообновление если страница не видна
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        }
    });
    @endif
});
</script>
@endpush
