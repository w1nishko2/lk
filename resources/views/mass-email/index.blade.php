@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-envelope"></i> Управление массовой рассылкой
            </h1>
            
            <!-- Statistics Cards -->
            <div class="row mb-4" id="stats-container">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 id="total-count">{{ $stats['total'] }}</h4>
                                    <p class="mb-0">Всего email</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-database fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 id="pending-count">{{ $stats['pending'] }}</h4>
                                    <p class="mb-0">Ожидают отправки</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 id="sent-count">{{ $stats['sent'] }}</h4>
                                    <p class="mb-0">Отправлено</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 id="failed-count">{{ $stats['failed'] }}</h4>
                                    <p class="mb-0">Ошибки</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Прогресс рассылки</h5>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $stats['total'] > 0 ? round(($stats['sent'] / $stats['total']) * 100, 2) : 0 }}%"
                             id="progress-bar">
                            <span id="progress-text">
                                {{ $stats['total'] > 0 ? round(($stats['sent'] / $stats['total']) * 100, 2) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-cogs"></i> Панель управления</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Импорт email адресов</h6>
                            <p class="text-muted">Загрузить email адреса из файла baseemail/Mail.txt</p>
                            <button class="btn btn-info" onclick="importEmails()">
                                <i class="fas fa-upload"></i> Импортировать email
                            </button>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Отправка писем</h6>
                            <div class="form-group">
                                <label for="batch-size">Количество писем за раз:</label>
                                <select class="form-control" id="batch-size" style="width: 120px; display: inline-block;">
                                    <option value="1">1</option>
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <button class="btn btn-success" onclick="sendBatch()">
                                <i class="fas fa-paper-plane"></i> Отправить пакет
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-warning" onclick="resetFailed()">
                                <i class="fas fa-redo"></i> Сбросить ошибки
                            </button>
                        </div>
                        
                        <div class="col-md-4">
                            <button class="btn btn-secondary" onclick="refreshStats()">
                                <i class="fas fa-sync"></i> Обновить статистику
                            </button>
                        </div>
                        
                        <div class="col-md-4">
                            <button class="btn btn-danger" onclick="clearQueue()" 
                                    onclick="return confirm('Вы уверены? Это удалит ВСЕ email из очереди!')">
                                <i class="fas fa-trash"></i> Очистить очередь
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Auto-send Control -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-robot"></i> Автоматическая отправка</h5>
                </div>
                <div class="card-body">
                    <p>Для автоматической отправки каждые 15 секунд добавьте в crontab:</p>
                    <div class="alert alert-info">
                        <code>*/1 * * * * cd {{ base_path() }} && php artisan email:send-mass --send --batch=5</code>
                    </div>
                    <p class="text-muted">
                        Это будет отправлять по 5 писем каждую минуту. 
                        Для отправки каждые 15 секунд используйте 4 задачи со сдвигом:
                    </p>
                    <div class="alert alert-secondary">
                        <code>* * * * * cd {{ base_path() }} && php artisan email:send-mass --send --batch=5</code><br>
                        <code>* * * * * sleep 15 && cd {{ base_path() }} && php artisan email:send-mass --send --batch=5</code><br>
                        <code>* * * * * sleep 30 && cd {{ base_path() }} && php artisan email:send-mass --send --batch=5</code><br>
                        <code>* * * * * sleep 45 && cd {{ base_path() }} && php artisan email:send-mass --send --batch=5</code>
                    </div>
                    <a href="{{ route('mass-email.template') }}" class="btn btn-outline-info" target="_blank">
                        <i class="fas fa-eye"></i> Предварительный просмотр шаблона
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-check-circle text-success"></i> Последние отправленные</h6>
                        </div>
                        <div class="card-body">
                            @if($recentSent->count() > 0)
                                <div class="list-group">
                                    @foreach($recentSent as $email)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $email->email }}</span>
                                            <small class="text-muted">{{ $email->sent_at->diffForHumans() }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Нет отправленных писем</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-exclamation-triangle text-danger"></i> Последние ошибки</h6>
                        </div>
                        <div class="card-body">
                            @if($recentFailed->count() > 0)
                                <div class="list-group">
                                    @foreach($recentFailed as $email)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $email->email }}</span>
                                                <small class="text-muted">Попыток: {{ $email->attempts }}</small>
                                            </div>
                                            @if($email->error_message)
                                                <small class="text-danger">{{ Str::limit($email->error_message, 100) }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Нет ошибок</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Загрузка...</span>
                </div>
                <p class="mt-2" id="loading-text">Обработка...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let statsRefreshInterval;

// Auto refresh stats every 30 seconds
$(document).ready(function() {
    statsRefreshInterval = setInterval(refreshStats, 30000);
});

function importEmails() {
    showLoading('Импорт email адресов...');
    
    $.post('{{ route("mass-email.import") }}', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            toastr.success(response.message);
            refreshStats();
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function(xhr) {
        hideLoading();
        toastr.error('Ошибка при импорте: ' + xhr.responseJSON?.message || 'Неизвестная ошибка');
    });
}

function sendBatch() {
    const batchSize = $('#batch-size').val();
    showLoading('Отправка пакета писем...');
    
    $.post('{{ route("mass-email.send-batch") }}', {
        _token: '{{ csrf_token() }}',
        batch_size: batchSize
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            toastr.success(response.message);
            refreshStats();
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function(xhr) {
        hideLoading();
        toastr.error('Ошибка при отправке: ' + xhr.responseJSON?.message || 'Неизвестная ошибка');
    });
}

function resetFailed() {
    if (!confirm('Вы уверены, что хотите сбросить все ошибки?')) {
        return;
    }
    
    showLoading('Сброс ошибок...');
    
    $.post('{{ route("mass-email.reset-failed") }}', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            toastr.success(response.message);
            refreshStats();
            location.reload(); // Refresh to update recent failed list
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function(xhr) {
        hideLoading();
        toastr.error('Ошибка при сбросе: ' + xhr.responseJSON?.message || 'Неизвестная ошибка');
    });
}

function clearQueue() {
    if (!confirm('ВНИМАНИЕ! Это удалит ВСЕ email из очереди. Вы уверены?')) {
        return;
    }
    
    showLoading('Очистка очереди...');
    
    $.post('{{ route("mass-email.clear") }}', {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        hideLoading();
        if (response.success) {
            toastr.success(response.message);
            refreshStats();
            location.reload(); // Refresh the page
        } else {
            toastr.error(response.message);
        }
    })
    .fail(function(xhr) {
        hideLoading();
        toastr.error('Ошибка при очистке: ' + xhr.responseJSON?.message || 'Неизвестная ошибка');
    });
}

function refreshStats() {
    $.get('{{ route("mass-email.stats") }}')
    .done(function(stats) {
        $('#total-count').text(stats.total);
        $('#pending-count').text(stats.pending);
        $('#sent-count').text(stats.sent);
        $('#failed-count').text(stats.failed);
        
        $('#progress-bar').css('width', stats.progress + '%');
        $('#progress-text').text(stats.progress + '%');
    })
    .fail(function() {
        toastr.error('Ошибка при обновлении статистики');
    });
}

function showLoading(text) {
    $('#loading-text').text(text);
    $('#loadingModal').modal('show');
}

function hideLoading() {
    $('#loadingModal').modal('hide');
}

// Initialize toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000"
};
</script>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush
