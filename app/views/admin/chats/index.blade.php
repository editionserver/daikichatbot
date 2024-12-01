@extends('layouts.admin')

@section('title', 'Sohbet Yönetimi')

@section('content')
<div class="container-fluid py-4">
    <!-- İstatistik Kartları -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
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
                        <span class="text-success text-sm font-weight-bolder">+{{ $stats['chat_growth'] }}% </span>
                        geçen aya göre
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
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
                        ortalama mesaj/sohbet
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
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
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">sentiment_satisfied</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Memnuniyet Oranı</p>
                        <h4 class="mb-0">%{{ number_format($stats['satisfaction_rate'], 1) }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        Son 30 günün ortalaması
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ana Kart -->
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center px-4">
                            <h6 class="text-white text-capitalize mb-0">Sohbet Listesi</h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm bg-gradient-dark me-2" id="exportCSV">
                                    <i class="material-icons text-sm">file_download</i> CSV
                                </button>
                                <button type="button" class="btn btn-sm bg-gradient-dark me-2" id="exportExcel">
                                    <i class="material-icons text-sm">table_view</i> Excel
                                </button>
                                <button type="button" class="btn btn-sm bg-gradient-dark" id="exportPDF">
                                    <i class="material-icons text-sm">picture_as_pdf</i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <!-- Filtreler -->
                    <div class="row mx-4 mb-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label>Tarih Aralığı</label>
                                <select class="form-control" id="dateFilter">
                                    <option value="today">Bugün</option>
                                    <option value="yesterday">Dün</option>
                                    <option value="last_7">Son 7 Gün</option>
                                    <option value="last_30">Son 30 Gün</option>
                                    <option value="this_month">Bu Ay</option>
                                    <option value="last_month">Geçen Ay</option>
                                    <option value="custom">Özel Aralık</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label>Durum</label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">Tümü</option>
                                    <option value="active">Aktif</option>
                                    <option value="closed">Kapanmış</option>
                                    <option value="waiting">Beklemede</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-4">
                                <label class="form-label">Ara (Kullanıcı, ID veya İçerik)</label>
                                <input type="text" class="form-control" id="searchInput">
                            </div>
                        </div>
                    </div>

                    <!-- Sohbet Listesi -->
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="chatsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı/ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Son Mesaj</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mesaj</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durum</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Süre</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tarih</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($chats as $chat)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($chat->user && $chat->user->profile_photo)
                                                    <img src="{{ asset('storage/'.$chat->user->profile_photo) }}" 
                                                         class="avatar avatar-sm me-3">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary">
                                                        {{ $chat->user ? $chat->user->getInitials() : 'M' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $chat->user ? $chat->user->name : 'Misafir Kullanıcı' }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">#{{ $chat->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ Str::limit($chat->last_message, 50) }}
                                        </p>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ $chat->messages_count }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @switch($chat->status)
                                            @case('active')
                                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                                @break
                                            @case('closed')
                                                <span class="badge badge-sm bg-gradient-secondary">Kapanmış</span>
                                                @break
                                            @case('waiting')
                                                <span class="badge badge-sm bg-gradient-warning">Beklemede</span>
                                                @break
                                            @default
                                                <span class="badge badge-sm bg-gradient-secondary">{{ $chat->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $chat->getDuration() }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $chat->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.chats.show', $chat->id) }}">
                                                        <i class="material-icons">visibility</i> Detay
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.chats.export', ['id' => $chat->id, 'format' => 'pdf']) }}">
                                                        <i class="material-icons">download</i> PDF Export
                                                    </a>
                                                </li>
                                                @if($chat->status === 'active')
                                                    <li>
                                                        <form action="{{ route('admin.chats.close', $chat->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="material-icons">close</i> Kapat
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('admin.chats.destroy', $chat->id) }}" method="POST" 
                                                          onsubmit="return confirm('Bu sohbeti silmek istediğinizden emin misiniz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="material-icons">delete</i> Sil
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row px-4 mt-4">
                        <div class="col-7">
                            <div class="text-sm">
                                Toplam <b>{{ $chats->total() }}</b> sohbetten 
                                <b>{{ $chats->firstItem() }}-{{ $chats->lastItem() }}</b> arası gösteriliyor
                            </div>
                        </div>
                        <div class="col-5">
                            {{ $chats->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Sohbet İstatistikleri</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm active" data-period="daily">
                                Günlük
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-period="weekly">
                                Haftalık
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-period="monthly">
                                Aylık
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chatStatsChart" class="chart-canvas" height="300"></canvas>
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
                            <input type="date" class="form-control" id="startDate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label>Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="applyDateRange">Uygula</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// DataTable initialize
const table = initializeDataTable('chatsTable', {
    order: [[5, 'desc']],
    columnDefs: [
        { orderable: false, targets: [6] }
    ]
});

// Filtreler
document.getElementById('dateFilter').addEventListener('change', function() {
    if (this.value === 'custom') {
        const modal = new bootstrap.Modal(document.getElementById('dateRangeModal'));
        modal.show();
    } else {
        updateFilters();
    }
});

document.getElementById('statusFilter').addEventListener('change', updateFilters);
document.getElementById('searchInput').addEventListener('keyup', debounce(updateFilters, 500));

document.getElementById('applyDateRange').addEventListener('click', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('dateRangeModal'));
    modal.hide();
    updateFilters();
});

function updateFilters() {
    const dateFilter = document.getElementById('dateFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchTerm = document.getElementById('searchInput').value;
    
    let dateParams = '';
    if (dateFilter === 'custom') {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        dateParams = `start_date=${startDate}&end_date=${endDate}`;
    } else {
        dateParams = `date_filter=${dateFilter}`;
    }

    window.location.href = `${window.location.pathname}?${dateParams}&status=${statusFilter}&search=${searchTerm}`;
}

// Grafik
var ctx = document.getElementById('chatStatsChart').getContext('2d');
var chatStatsChart;

function updateChart(period = 'daily') {
    fetch(`/admin/chats/stats?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (chatStatsChart) {
                chatStatsChart.destroy();
            }

            chatStatsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Toplam Sohbet',
                            data: data.chats,
                            borderColor: '#e91e63',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(233, 30, 99, 0.1)'
                        },
                        {
                            label: 'Aktif Sohbet',
                            data: data.active_chats,
                            borderColor: '#4CAF50',
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
                                color: 'rgba(0, 0, 0, 0.1)'
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
        });
}

// İlk yükleme
updateChart();

// Periyot değiştirme butonları
document.querySelectorAll('[data-period]').forEach(button => {
    button.addEventListener('click', function() {
        // Aktif butonu güncelle
        document.querySelector('[data-period].active').classList.remove('active');
        this.classList.add('active');
        
        // Grafiği güncelle
        updateChart(this.dataset.period);
    });
});

// Export işlemleri
document.getElementById('exportCSV').addEventListener('click', function() {
    window.location.href = `/admin/chats/export?format=csv&${getFilterParams()}`;
});

document.getElementById('exportExcel').addEventListener('click', function() {
    window.location.href = `/admin/chats/export?format=xlsx&${getFilterParams()}`;
});

document.getElementById('exportPDF').addEventListener('click', function() {
    window.location.href = `/admin/chats/export?format=pdf&${getFilterParams()}`;
});

function getFilterParams() {
    const dateFilter = document.getElementById('dateFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchTerm = document.getElementById('searchInput').value;
    
    let params = [];
    
    if (dateFilter === 'custom') {
        params.push(`start_date=${document.getElementById('startDate').value}`);
        params.push(`end_date=${document.getElementById('endDate').value}`);
    } else {
        params.push(`date_filter=${dateFilter}`);
    }
    
    if (statusFilter) params.push(`status=${statusFilter}`);
    if (searchTerm) params.push(`search=${searchTerm}`);
    
    return params.join('&');
}
</script>
@endpush