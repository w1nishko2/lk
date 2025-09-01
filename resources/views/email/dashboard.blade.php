@extends('layouts.app')

@section('title', 'Email Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">📧 Email Dashboard</h1>
                    <p class="text-muted">Мониторинг и управление email кампаниями</p>
                </div>
                <div>
                    <a href="{{ route('email.campaigns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать кампанию
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Всего кампаний
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_campaigns'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Активные кампании
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_campaigns'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Подписчиков
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_subscribers']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Отправлено сегодня
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['emails_sent_today']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Средний % открытий</div>
                    <div class="h4 mb-0 font-weight-bold text-success">{{ $stats['average_open_rate'] }}%</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Средний % кликов</div>
                    <div class="h4 mb-0 font-weight-bold text-info">{{ $stats['average_click_rate'] }}%</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">% отказов</div>
                    <div class="h4 mb-0 font-weight-bold text-warning">{{ $stats['bounce_rate'] }}%</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Отправлено за месяц</div>
                    <div class="h4 mb-0 font-weight-bold text-primary">{{ number_format($stats['emails_sent_this_month']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Отправка писем (последние 30 дней)</h6>
                </div>
                <div class="card-body">
                    <canvas id="emailsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🎯 Производительность кампаний</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Последние кампании</h6>
                    <a href="{{ route('email.campaigns.index') }}" class="btn btn-sm btn-outline-primary">
                        Все кампании
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="campaignsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Статус</th>
                                    <th>Отправлено</th>
                                    <th>Всего получателей</th>
                                    <th>% успеха</th>
                                    <th>% открытий</th>
                                    <th>Создана</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCampaigns as $campaign)
                                <tr>
                                    <td>{{ $campaign['id'] }}</td>
                                    <td>
                                        <a href="{{ route('email.campaigns.show', $campaign['id']) }}">
                                            {{ $campaign['name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $campaign['status'] === 'completed' ? 'success' : ($campaign['status'] === 'sending' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($campaign['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($campaign['sent_count']) }}</td>
                                    <td>{{ number_format($campaign['total_recipients']) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $campaign['success_rate'] }}%" 
                                                 aria-valuenow="{{ $campaign['success_rate'] }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($campaign['success_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ $campaign['open_rate'] }}%" 
                                                 aria-valuenow="{{ $campaign['open_rate'] }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($campaign['open_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $campaign['created_at'] }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('email.campaigns.show', $campaign['id']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($campaign['status'] === 'sending')
                                            <button class="btn btn-sm btn-outline-warning pause-campaign" 
                                                    data-id="{{ $campaign['id'] }}">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Updates -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info" id="realtime-status">
                <i class="fas fa-sync fa-spin"></i> Получение актуальных данных...
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Emails per day chart
const emailsCtx = document.getElementById('emailsChart').getContext('2d');
const emailsChart = new Chart(emailsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['emails_per_day']->pluck('date')) !!},
        datasets: [{
            label: 'Писем отправлено',
            data: {!! json_encode($chartData['emails_per_day']->pluck('count')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Статистика отправки писем'
            }
        }
    },
});

// Campaign performance chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Отправлено', 'Открыто', 'Кликнуто'],
        datasets: [{
            data: [
                {{ $stats['emails_sent_this_month'] }},
                {{ intval($stats['emails_sent_this_month'] * $stats['average_open_rate'] / 100) }},
                {{ intval($stats['emails_sent_this_month'] * $stats['average_click_rate'] / 100) }}
            ],
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Real-time updates
function updateRealtimeStats() {
    fetch('{{ route("email.dashboard.realtime") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('realtime-status').innerHTML = 
                '<i class="fas fa-check text-success"></i> ' +
                `Очередь: ${data.queue_size} писем | ` +
                `Ошибок: ${data.failed_jobs} | ` +
                `Обновлено: ${new Date().toLocaleTimeString()}`;
        })
        .catch(error => {
            document.getElementById('realtime-status').innerHTML = 
                '<i class="fas fa-exclamation-triangle text-warning"></i> Ошибка получения данных';
        });
}

// Update every 30 seconds
setInterval(updateRealtimeStats, 30000);
updateRealtimeStats();

// Pause/Resume campaign handlers
document.querySelectorAll('.pause-campaign').forEach(button => {
    button.addEventListener('click', function() {
        const campaignId = this.dataset.id;
        
        fetch(`/email/campaigns/${campaignId}/pause`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        });
    });
});
</script>
@endpush
@endsection
