@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-envelope-bulk text-primary me-2"></i>
                    Email Рассылки
                </h1>
                <a href="{{ route('email-campaigns.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Создать рассылку
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if($campaigns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Название</th>
                                        <th>Статус</th>
                                        <th>Получатели</th>
                                        <th>Прогресс</th>
                                        <th>Создана</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campaigns as $campaign)
                                        <tr>
                                            <td>
                                                <strong>{{ $campaign->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $campaign->subject }}</small>
                                            </td>
                                            <td>
                                                @switch($campaign->status)
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
                                            <td>
                                                <div class="small">
                                                    <div class="text-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Отправлено: {{ number_format($campaign->sent_count) }}
                                                    </div>
                                                    <div class="text-danger">
                                                        <i class="fas fa-times me-1"></i>
                                                        Ошибки: {{ number_format($campaign->failed_count) }}
                                                    </div>
                                                    <div class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        Всего: {{ number_format($campaign->total_recipients) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($campaign->total_recipients > 0)
                                                    <div class="progress mb-1" style="height: 20px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: {{ $campaign->progress_percentage }}%">
                                                            {{ number_format($campaign->progress_percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ number_format($campaign->sent_count) }} из {{ number_format($campaign->total_recipients) }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Нет получателей</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $campaign->created_at->format('d.m.Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('email-campaigns.show', $campaign) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Подробности">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($campaign->canStart())
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success start-campaign" 
                                                                data-campaign-id="{{ $campaign->id }}"
                                                                title="Запустить">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($campaign->isSending())
                                                        <button type="button" 
                                                                class="btn btn-sm btn-warning pause-campaign" 
                                                                data-campaign-id="{{ $campaign->id }}"
                                                                title="Приостановить">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('email-campaigns.preview', $campaign) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       target="_blank"
                                                       title="Предпросмотр">
                                                        <i class="fas fa-search"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $campaigns->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open-text fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Рассылки не найдены</h4>
                            <p class="text-muted mb-4">Создайте свою первую email рассылку для привлечения клиентов</p>
                            <a href="{{ route('email-campaigns.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Создать рассылку
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Запуск кампании
    document.querySelectorAll('.start-campaign').forEach(button => {
        button.addEventListener('click', function() {
            const campaignId = this.dataset.campaignId;
            
            if (confirm('Запустить рассылку? Это действие нельзя будет отменить.')) {
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
                    }
                })
                .catch(error => {
                    alert('Произошла ошибка при запуске рассылки');
                });
            }
        });
    });

    // Приостановка кампании
    document.querySelectorAll('.pause-campaign').forEach(button => {
        button.addEventListener('click', function() {
            const campaignId = this.dataset.campaignId;
            
            if (confirm('Приостановить рассылку?')) {
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
                    }
                })
                .catch(error => {
                    alert('Произошла ошибка при приостановке рассылки');
                });
            }
        });
    });
    
    // Обновление статуса каждые 10 секунд
    setInterval(() => {
        if (document.querySelector('.badge:contains("Отправляется")')) {
            location.reload();
        }
    }, 10000);
});
</script>
@endpush
