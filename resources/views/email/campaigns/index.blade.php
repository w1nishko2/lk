@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Email Рассылки</h5>
                    <a href="{{ route('email-campaigns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать рассылку
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Тема</th>
                                    <th>Статус</th>
                                    <th>Получатели</th>
                                    <th>Отправлено</th>
                                    <th>Ошибки</th>
                                    <th>Создана</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->name }}</td>
                                        <td>{{ Str::limit($campaign->subject, 50) }}</td>
                                        <td>
                                            @switch($campaign->status)
                                                @case('draft')
                                                    <span class="badge bg-secondary">Черновик</span>
                                                    @break
                                                @case('active')
                                                    <span class="badge bg-warning">Активна</span>
                                                    @break
                                                @case('paused')
                                                    <span class="badge bg-info">Приостановлена</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Завершена</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">Ошибка</span>
                                                    @break
                                                @case('scheduled')
                                                    <span class="badge bg-primary">Запланирована</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ number_format($campaign->recipients_count) }}</td>
                                        <td>{{ number_format($campaign->sent_count) }}</td>
                                        <td>{{ number_format($campaign->failed_count) }}</td>
                                        <td>{{ $campaign->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('email-campaigns.show', $campaign) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($campaign->status === 'draft')
                                                    <a href="{{ route('email-campaigns.edit', $campaign) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($campaign->status === 'draft' || $campaign->status === 'paused')
                                                    <form method="POST" action="{{ route('email-campaigns.start', $campaign) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                                onclick="return confirm('Запустить рассылку?')">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($campaign->status === 'active')
                                                    <form method="POST" action="{{ route('email-campaigns.pause', $campaign) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                                                onclick="return confirm('Приостановить рассылку?')">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($campaign->status !== 'active')
                                                    <form method="POST" action="{{ route('email-campaigns.destroy', $campaign) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Удалить рассылку?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Нет созданных рассылок</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
