@extends('layouts.admin')

@section('title', 'Kullanıcı Detayı')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Profil Kartı -->
        <div class="col-md-4">
            <div class="card card-profile">
                <div class="position-relative">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg p-3">
                        <div class="row mt-5">
                            <div class="col-12 text-center">
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                                         class="avatar avatar-xxl rounded-circle mt-n7 border border-4 border-white">
                                @else
                                    <div class="avatar avatar-xxl bg-gradient-secondary rounded-circle mt-n7 border border-4 border-white">
                                        {{ $user->getInitials() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body text-center mt-4">
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>

                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <span class="badge bg-gradient-{{ $user->plan->isPro() ? 'success' : 'secondary' }}">
                                {{ $user->plan->name }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-gradient-{{ $user->isActive() ? 'success' : 'warning' }}">
                                {{ $user->status }}
                            </span>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-4">
                            <h6>{{ $user->chats_count }}</h6>
                            <p class="text-muted mb-0">Sohbet</p>
                        </div>
                        <div class="col-4">
                            <h6>{{ $user->messages_count }}</h6>
                            <p class="text-muted mb-0">Mesaj</p>
                        </div>
                        <div class="col-4">
                            <h6>{{ $user->response_count }}</h6>
                            <p class="text-muted mb-0">Yanıt</p>
                        </div>
                    </div>

                    <hr class="horizontal dark mt-4">

                    <div class="text-start px-3">
                        <p><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Son Giriş:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : '-' }}</p>
                        @if($user->notes)
                            <p><strong>Notlar:</strong><br>{{ $user->notes }}</p>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="material-icons">edit</i> Düzenle
                        </a>
                        <button type="button" 
                                class="btn btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal">
                            <i class="material-icons">delete</i> Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="col-md-8">
            <!-- Kullanıcı İstatistikleri -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kullanım İstatistikleri</h5>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="activityChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Son Sohbetler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Son Sohbetler</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Başlık</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mesaj</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tarih</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->chats()->latest()->limit(5)->get() as $chat)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ Str::limit($chat->title, 30) }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $chat->messages_count }} mesaj
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $chat->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('admin.chats.show', $chat->id) }}" 
                                           class="text-secondary font-weight-bold text-xs">
                                            Detay
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aktivite Geçmişi -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Aktivite Geçmişi</h5>
                </div>
                <div class="card-body">
                <div class="timeline timeline-one-side">
                        @foreach($activities as $activity)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="material-icons text-{{ $activity->type_color }}">{{ $activity->icon }}</i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    {{ $activity->description }}
                                </h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $activity->created_at->format('d M H:i') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- API Kullanımı -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">API Kullanımı</h5>
                </div>
                <div class="card-body">
                    @if($user->api_token)
                        <div class="alert alert-info" role="alert">
                            <strong>API Token:</strong> 
                            <code>{{ $user->api_token }}</code>
                            <button class="btn btn-sm btn-link" onclick="copyToClipboard('{{ $user->api_token }}')">
                                <i class="material-icons">content_copy</i>
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Toplam İstek</p>
                                                <h4 class="mb-0">{{ $apiStats['total_requests'] }}</h4>
                                            </div>
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                                <span class="avatar-title">
                                                    <i class="material-icons">api</i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Başarılı İstek</p>
                                                <h4 class="mb-0">{{ $apiStats['successful_requests'] }}</h4>
                                            </div>
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                                                <span class="avatar-title">
                                                    <i class="material-icons">check</i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Hatalı İstek</p>
                                                <h4 class="mb-0">{{ $apiStats['failed_requests'] }}</h4>
                                            </div>
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-danger align-self-center">
                                                <span class="avatar-title">
                                                    <i class="material-icons">error</i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <p>API erişimi henüz aktif değil.</p>
                            <button class="btn btn-primary" onclick="generateApiToken()">
                                <i class="material-icons">vpn_key</i> API Token Oluştur
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcıyı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="material-icons">delete</i> Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Aktivite grafiği
var ctx = document.getElementById('activityChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($activityData['labels']) !!},
        datasets: [{
            label: 'Mesajlar',
            data: {!! json_encode($activityData['messages']) !!},
            borderColor: '#e91e63',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(233, 30, 99, 0.1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2],
                    borderDashOffset: [2],
                    drawBorder: false,
                    drawTicks: false
                },
                ticks: {
                    padding: 10
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    drawTicks: false,
                    drawOnChartArea: false
                },
                ticks: {
                    padding: 10
                }
            }
        }
    }
});

// API Token işlemleri
function generateApiToken() {
    fetch(`/admin/users/${user.id}/generate-token`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification('Token oluşturulurken bir hata oluştu', 'error');
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Token panoya kopyalandı', 'success');
    });
}
</script>
@endpush