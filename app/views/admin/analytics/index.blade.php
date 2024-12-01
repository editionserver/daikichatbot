@extends('layouts.admin')

@section('title', 'Analitik Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Filtreler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row align-items-end">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-0">
                                <label>Tarih Aralığı</label>
                                <select class="form-control" name="date_range">
                                    <option value="today">Bugün</option>
                                    <option value="yesterday">Dün</option>
                                    <option value="last_7">Son 7 Gün</option>
                                    <option value="last_30" selected>Son 30 Gün</option>
                                    <option value="this_month">Bu Ay</option>
                                    <option value="last_month">Geçen Ay</option>
                                    <option value="custom">Özel Aralık</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-0">
                                <label>Kullanıcı Planı</label>
                                <select class="form-control" name="plan">
                                    <option value="">Tümü</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn bg-gradient-primary mb-0 w-100">
                                <i class="material-icons">filter_alt</i>
                                Filtrele
                            </button>
                        </div>
                        <div class="col-md-3">
                            <div class="dropdown">
                                <button class="btn bg-gradient-dark dropdown-toggle mb-0 w-100" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="material-icons">file_download</i>
                                    Rapor İndir
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                                            <i class="material-icons">picture_as_pdf</i>
                                            PDF Rapor
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportReport('excel')">
                                            <i class="material-icons">table_view</i>
                                            Excel Rapor
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportReport('json')">
                                            <i class="material-icons">code</i>
                                            JSON Veri
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Özet İstatistikler -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Aktif Kullanıcılar</p>
                        <h4 class="mb-0">{{ number_format($stats['active_users']) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        @if($stats['user_growth'] > 0)
                            <span class="text-success text-sm font-weight-bolder">+{{ $stats['user_growth'] }}% </span>
                        @else
                            <span class="text-danger text-sm font-weight-bolder">{{ $stats['user_growth'] }}% </span>
                        @endif
                        geçen döneme göre
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">chat</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Toplam Sohbet</p>
                        <h4 class="mb-0">{{ number_format($stats['total_chats']) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">{{ $stats['avg_chats_per_user'] }}</span>
                        sohbet/kullanıcı
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">message</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Toplam Mesaj</p>
                        <h4 class="mb-0">{{ number_format($stats['total_messages']) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-info text-sm font-weight-bolder">{{ $stats['avg_messages_per_chat'] }}</span>
                        mesaj/sohbet
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">timer</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Ort. Yanıt Süresi</p>
                        <h4 class="mb-0">{{ number_format($stats['avg_response_time'], 1) }}s</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        @if($stats['response_time_change'] < 0)
                            <span class="text-success text-sm font-weight-bolder">{{ $stats['response_time_change'] }}% </span>
                            daha hızlı
                        @else
                            <span class="text-danger text-sm font-weight-bolder">+{{ $stats['response_time_change'] }}% </span>
                            daha yavaş
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafikler -->
    <div class="row mb-4">
        <!-- Aktivite Grafiği -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Aktivite Analizi</h6>
                        <div class="ms-auto">
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm mb-0 active"
                                        onclick="updateActivityChart('messages')">
                                    Mesajlar
                                </button>
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm mb-0"
                                        onclick="updateActivityChart('users')">
                                    Kullanıcılar
                                </button>
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm mb-0"
                                        onclick="updateActivityChart('response_time')">
                                    Yanıt Süresi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="activityChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dağılım Grafikleri -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Kullanıcı Dağılımı</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="userDistributionChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alt Grafikler -->
    <div class="row mb-4">
        <!-- Saat Bazlı Analiz -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Saat Bazlı Aktivite</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="hourlyActivityChart" class="chart-canvas" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Haftalık Analiz -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Haftalık Aktivite</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="weeklyActivityChart" class="chart-canvas" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- En Aktif Kullanıcılar ve En Çok Kullanılan Yanıtlar -->
    <div class="row">
        <!-- En Aktif Kullanıcılar -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">En Aktif Kullanıcılar</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Plan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sohbetler</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mesajlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($user->profile_photo)
                                                    <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                                                         class="avatar avatar-sm me-3">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary">
                                                        {{ $user->getInitials() }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm {{ $user->plan->isPro() ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                            {{ $user->plan->name }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ $user->chats_count }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-warning">
                                            {{ $user->messages_count }}
                                        </span>
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

        <!-- En Çok Kullanılan Yanıtlar -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">En Çok Kullanılan Yanıtlar</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Anahtar Kelime</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Başarı Oranı</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanım</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Son Kullanım</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topResponses as $response)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($response->hasRegexKeyword())
                                                    <i class="material-icons text-warning">code</i>
                                                @else
                                                    <i class="material-icons text-info">text_fields</i>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-2">
                                                <h6 class="mb-0 text-sm">{{ $response->keyword }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ Str::limit($response->response_text, 30) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-sm me-2">{{ number_format($response->success_rate) }}%</span>
                                            <div class="progress" style="width: 100px; height: 3px;">
                                                <div class="progress-bar bg-gradient-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $response->success_rate }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ number_format($response->usage_count) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $response->last_used_at ? $response->last_used_at->diffForHumans() : '-' }}
                                        </span>
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

<!-- Tarih Aralığı Modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tarih Aralığı Seçin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label>Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label>Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="applyDateRange()">Uygula</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Aktivite grafiği
var activityChart;
var activityChartData = {
    messages: {
        labels: {!! json_encode($activityData['labels']) !!},
        datasets: [
            {
                label: 'Toplam Mesaj',
                data: {!! json_encode($activityData['messages']) !!},
                borderColor: '#e91e63',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(233, 30, 99, 0.1)'
            },
            {
                label: 'Bot Mesajları',
                data: {!! json_encode($activityData['bot_messages']) !!},
                borderColor: '#4caf50',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(76, 175, 80, 0.1)'
            }
        ]
    },
    users: {
        labels: {!! json_encode($activityData['labels']) !!},
        datasets: [{
            label: 'Aktif Kullanıcılar',
            data: {!! json_encode($activityData['users']) !!},
            borderColor: '#2196f3',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(33, 150, 243, 0.1)'
        }]
    },
    response_time: {
        labels: {!! json_encode($activityData['labels']) !!},
        datasets: [{
            label: 'Ortalama Yanıt Süresi (s)',
            data: {!! json_encode($activityData['response_time']) !!},
            borderColor: '#ff9800',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(255, 152, 0, 0.1)'
        }]
    }
};

function initActivityChart(type = 'messages') {
    var ctx = document.getElementById('activityChart').getContext('2d');
    if (activityChart) {
        activityChart.destroy();
    }

    activityChart = new Chart(ctx, {
        type: 'line',
        data: activityChartData[type],
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
                        borderDash: [2],
                        drawBorder: false,
                        drawTicks: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        drawTicks: false,
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

// İlk yükleme
initActivityChart();

// Kullanıcı dağılımı grafiği
var distributionCtx = document.getElementById('userDistributionChart').getContext('2d');
new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($distributionData['labels']) !!},
        datasets: [{
            data: {!! json_encode($distributionData['data']) !!},
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

// Saat bazlı aktivite grafiği
var hourlyCtx = document.getElementById('hourlyActivityChart').getContext('2d');
new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
        datasets: [{
            label: 'Mesaj Sayısı',
            data: {!! json_encode($hourlyData) !!},
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

// Haftalık aktivite grafiği
var weeklyCtx = document.getElementById('weeklyActivityChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'],
        datasets: [{
            label: 'Mesaj Sayısı',
            data: {!! json_encode($weeklyData) !!},
            backgroundColor: 'rgba(76, 175, 80, 0.8)'
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

// Filtre işlemleri
document.querySelector('select[name="date_range"]').addEventListener('change', function(e) {
    if (e.target.value === 'custom') {
        const modal = new bootstrap.Modal(document.getElementById('dateRangeModal'));
        modal.show();
    } else {
        document.getElementById('filterForm').submit();
    }
});

function applyDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        showNotification('Lütfen başlangıç ve bitiş tarihlerini seçin', 'warning');
        return;
    }

    if (new Date(startDate) > new Date(endDate)) {
        showNotification('Başlangıç tarihi bitiş tarihinden büyük olamaz', 'warning');
        return;
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('dateRangeModal'));
    modal.hide();

    document.getElementById('filterForm').submit();
}

// Rapor export
function exportReport(format) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    formData.append('format', format);

    window.location.href = `/admin/analytics/export?${new URLSearchParams(formData).toString()}`;
}

// Grafik güncelleme
function updateActivityChart(type) {
    // Aktif butonu güncelle
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Grafiği güncelle
    initActivityChart(type);
}

// Dark/Light tema desteği
document.addEventListener('themeChanged', function(e) {
    const isDark = e.detail.isDark;
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
    
    [activityChart].forEach(chart => {
        if (chart) {
            chart.options.scales.y.grid.color = gridColor;
            chart.options.scales.x.grid.color = gridColor;
            chart.update();
        }
    });
});
</script>
@endpush