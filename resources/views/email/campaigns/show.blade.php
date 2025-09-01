@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-envelope"></i> {{ $emailCampaign->name }}</h2>
                    <p class="text-muted">{{ $emailCampaign->subject }}</p>
                </div>
                <div>
                    <a href="{{ route('email-campaigns.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                    @if($emailCampaign->status === 'draft')
                        <a href="{{ route('email-campaigns.edit', $emailCampaign) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Статистика -->
                <div class="col-md-12 mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ number_format($stats['total']) }}</h5>
                                    <p class="card-text text-muted">Всего получателей</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-success">{{ number_format($stats['sent']) }}</h5>
                                    <p class="card-text text-muted">Отправлено</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">{{ number_format($stats['pending']) }}</h5>
                                    <p class="card-text text-muted">В ожидании</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">{{ number_format($stats['failed']) }}</h5>
                                    <p class="card-text text-muted">Ошибки</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['success_rate'] }}%</h5>
                                    <p class="card-text text-muted">Успешность</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    @switch($emailCampaign->status)
                                        @case('draft')
                                            <span class="badge bg-secondary fs-6">Черновик</span>
                                            @break
                                        @case('active')
                                            <span class="badge bg-primary fs-6">Активна</span>
                                            @break
                                        @case('paused')
                                            <span class="badge bg-warning fs-6">Приостановлена</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success fs-6">Завершена</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger fs-6">Ошибка</span>
                                            @break
                                    @endswitch
                                    <p class="card-text text-muted mt-2">Статус</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Управление кампанией -->
                @if($emailCampaign->status === 'draft' || $emailCampaign->status === 'paused')
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Управление рассылкой</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('email-campaigns.start', $emailCampaign) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play"></i> 
                                        {{ $emailCampaign->status === 'paused' ? 'Возобновить рассылку' : 'Запустить рассылку' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($emailCampaign->status === 'active')
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Управление рассылкой</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('email-campaigns.pause', $emailCampaign) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-pause"></i> Приостановить рассылку
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Содержимое письма -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Содержимое письма</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label"><strong>Тема:</strong></label>
                                <p>{{ $emailCampaign->subject }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Содержание:</strong></label>
                                <div class="border p-3" style="max-height: 300px; overflow-y: auto;">
                                    {!! nl2br(e($emailCampaign->content)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Логи отправки -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Логи отправки</h5>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            @if($logs->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($logs as $log)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $log->email }}</strong>
                                                @if($log->error_message)
                                                    <br>
                                                    <small class="text-danger">{{ $log->error_message }}</small>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                @switch($log->status)
                                                    @case('sent')
                                                        <span class="badge bg-success">Отправлено</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">Ошибка</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">Ожидание</span>
                                                        @break
                                                @endswitch
                                                @if($log->sent_at)
                                                    <br>
                                                    <small class="text-muted">{{ $log->sent_at->format('d.m.Y H:i') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3">
                                    {{ $logs->links() }}
                                </div>
                            @else
                                <p class="text-muted">Логи отсутствуют</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
