@extends('layouts.admin')

@section('title', 'Yanıt Detayı')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sol Kolon - Yanıt Detayları -->
        <div class="col-lg-4">
            <!-- Temel Bilgiler -->
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">question_answer</i>
                    </div>
                    <div class="text-end pt-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Yanıt Detayları</h5>
                            <a href="{{ route('admin.responses.edit', $response->id) }}" class="btn btn-info btn-sm">
                                <i class="material-icons">edit</i> Düzenle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Anahtar Kelime</h6>
                        <div class="d-flex align-items-center">
                            @if($response->hasRegexKeyword())
                                <i class="material-icons text-warning me-2">code</i>
                                <code>{{ $response->keyword }}</code>
                            @else
                                <i class="material-icons text-info me-2">text_fields</i>
                                <span>{{ $response->keyword }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Yanıt Metni</h6>
                        <p class="text-sm">{{ $response->response_text }}</p>
                    </div>

                    @if($response->hasAttachments())
                        <div class="mb-4">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Dosya Ekleri</h6>
                            <div class="row">
                                @foreach($response->attachments as $attachment)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-2">
                                            @if(Str::startsWith($attachment['mime_type'], 'image/'))
                                                <img src="{{ asset('storage/'.$attachment['path']) }}" 
                                                     class="img-fluid rounded mb-2" 
                                                     alt="{{ $attachment['name'] }}">
                                            @else
                                                <i class="material-icons">attach_file</i>
                                            @endif
                                            <small class="d-block text-muted">{{ $attachment['name'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Ayarlar</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="material-icons me-2">priority_high</i>
                            <span>Öncelik: {{ ['Düşük', 'Normal', 'Yüksek'][$response->priority - 1] }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="material-icons me-2">{{ $response->is_active ? 'check_circle' : 'cancel' }}</i>
                            <span>Durum: {{ $response->is_active ? 'Aktif' : 'Pasif' }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="material-icons me-2">text_format</i>
                            <span>Büyük/Küçük Harf: {{ $response->case_sensitive ? 'Duyarlı' : 'Duyarsız' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="material-icons me-2">compare_arrows</i>
                            <span>Eşleşme: {{ $response->exact_match ? 'Tam' : 'Kısmi' }}</span>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Zaman Bilgileri</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="material-icons me-2">add_circle</i>
                            <span>Oluşturulma: {{ $response->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="material-icons me-2">update</i>
                            <span>Son Güncelleme: {{ $response->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="material-icons me-2">history</i>
                            <span>Son Kullanım: {{ $response->last_used_at ? $response->last_used_at->format('d.m.Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon - İstatistikler -->
        <div class="col-lg-8">
            <!-- Kullanım İstatistikleri -->
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">Kullanım İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6>Toplam Kullanım</h6>
                                <h4>{{ number_format($stats['total_usage']) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6>Son 30 Gün</h6>
                                <h4>{{ number_format($stats['monthly_usage']) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6>Ortalama/Gün</h6>
                                <h4>{{ number_format($stats['average_daily_usage'], 1) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6>Başarı Oranı</h6>
                                <h4>%{{ number_format($stats['success_rate'], 1) }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="chart mt-4">
                        <canvas id="usageChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kullanıcı Dağılımı -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı Dağılımı</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Üyelik Planları Dağılımı -->
                        <div class="col-md-6">
                            <div class="chart">
                                <canvas id="planDistributionChart" height="200"></canvas>
                            </div>
                        </div>
                        <!-- Kullanıcı Aktivitesi -->
                        <div class="col-md-6">
                            <div class="chart">
                                <canvas id="userActivityChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- En Son Kullanımlar -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Son Kullanımlar</h5>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Orijinal Mesaj</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tarih</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Başarılı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsages as $usage)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($usage->user && $usage->user->profile_photo)
                                                    <img src="{{ asset('storage/'.$usage->user->profile_photo) }}" 
                                                         class="avatar avatar-sm me-3">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary">
                                                        {{ $usage->user ? $usage->user->getInitials() : 'M' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $usage->user ? $usage->user->name : 'Misafir Kullanıcı' }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $usage->user ? $usage->user->email : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ Str::limit($usage->original_message, 50) }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $usage->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($usage->is_successful)
                                            <span class="badge badge-sm bg-gradient-success">Başarılı</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-danger">Başarısız</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Benzer Yanıtlar -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Benzer Yanıtlar</h5>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Anahtar Kelime</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Benzerlik</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanım</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($similarResponses as $similar)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($similar->hasRegexKeyword())
                                                    <i class="material-icons text-warning">code</i>
                                                @else
                                                    <i class="material-icons text-info">text_fields</i>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-2">
                                                <h6 class="mb-0 text-sm">{{ $similar->keyword }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-gradient-info" 
                                                 role="progressbar" 
                                                 style="width: {{ $similar->similarity_score }}%" 
                                                 aria-valuenow="{{ $similar->similarity_score }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="text-xs">%{{ number_format($similar->similarity_score, 1) }} benzerlik</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ $similar->usage_count }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('admin.responses.show', $similar->id) }}" 
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Kullanım grafiği
var usageCtx = document.getElementById('usageChart').getContext('2d');
new Chart(usageCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($usageData['labels']) !!},
        datasets: [
            {
                label: 'Günlük Kullanım',
                data: {!! json_encode($usageData['usage']) !!},
                borderColor: '#e91e63',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(233, 30, 99, 0.1)'
            },
            {
                label: 'Başarılı Kullanım',
                data: {!! json_encode($usageData['successful_usage']) !!},
                borderColor: '#4caf50',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(76, 175, 80, 0.1)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false,
                    drawTicks: false
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    drawTicks: false
                }
            }
        }
    }
});

// Plan dağılımı grafiği
var planCtx = document.getElementById('planDistributionChart').getContext('2d');
new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($planData['labels']) !!},
        datasets: [{
            data: {!! json_encode($planData['data']) !!},
            backgroundColor: [
                'rgba(233, 30, 99, 0.8)',
                'rgba(76, 175, 80, 0.8)',
                'rgba(33, 150, 243, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Kullanıcı aktivitesi grafiği
var activityCtx = document.getElementById('userActivityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'bar',
    data: {
        labels: ['Tek Seferlik', '2-5 Kez', '6-10 Kez', '10+ Kez'],
        datasets: [{
            label: 'Kullanıcı Sayısı',
            data: {!! json_encode($activityData) !!},
            backgroundColor: 'rgba(33, 150, 243, 0.8)'
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
                    drawBorder: false,
                    drawTicks: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>
@endpush