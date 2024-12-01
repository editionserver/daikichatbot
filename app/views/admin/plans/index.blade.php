@extends('layouts.admin')

@section('title', 'Üyelik Planları')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="row px-4">
                            <div class="col-6">
                                <h6 class="text-white text-capitalize">Üyelik Planları</h6>
                            </div>
                            <div class="col-6 text-end">
                                <a class="btn bg-gradient-dark mb-0" href="{{ route('admin.plans.create') }}">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Yeni Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="row px-4">
                        <!-- Özet İstatistikler -->
                        <div class="col-xl-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">people</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0">Toplam Kullanıcı</p>
                                        <h4 class="mb-0">{{ $totalUsers }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">workspace_premium</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0">Pro Kullanıcılar</p>
                                        <h4 class="mb-0">{{ $proUsers }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">paid</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0">Aylık Gelir</p>
                                        <h4 class="mb-0">{{ number_format($monthlyRevenue, 2) }} ₺</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">trending_up</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0">Dönüşüm Oranı</p>
                                        <h4 class="mb-0">%{{ number_format($conversionRate, 1) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Listesi -->
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Plan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fiyat</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mesaj Limiti</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durum</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $plan->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    @if($plan->is_popular)
                                                        <span class="badge bg-gradient-success">Popular</span>
                                                    @endif
                                                    @if($plan->is_recommended)
                                                        <span class="badge bg-gradient-info">Önerilen</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0 text-sm">{{ number_format($plan->monthly_price, 2) }} ₺</h6>
                                            <p class="text-xs text-secondary mb-0">aylık</p>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($plan->response_limit === -1)
                                            <span class="badge badge-sm bg-gradient-success">Sınırsız</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-info">{{ $plan->response_limit }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0 text-sm">{{ $plan->users_count }}</h6>
                                            <p class="text-xs text-secondary mb-0">kullanıcı</p>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($plan->is_active)
                                            <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.plans.edit', $plan->id) }}">
                                                        <i class="material-icons">edit</i> Düzenle
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.plans.show', $plan->id) }}">
                                                        <i class="material-icons">visibility</i> Detay
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @if($plan->is_active)
                                                    <li>
                                                        <form action="{{ route('admin.plans.deactivate', $plan->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="material-icons">pause</i> Pasifleştir
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.plans.activate', $plan->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="material-icons">play_arrow</i> Aktifleştir
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if(!$plan->is_default)
                                                    <li>
                                                        <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" 
                                                              onsubmit="return confirm('Bu planı silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="material-icons">delete</i> Sil
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
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

    <!-- Plan Karşılaştırma Grafiği -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Plan Karşılaştırması</h5>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="planComparisonChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var ctx = document.getElementById('planComparisonChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [
            {
                label: 'Kullanıcı Sayısı',
                data: {!! json_encode($chartData['users']) !!},
                backgroundColor: 'rgba(66, 133, 244, 0.6)',
                borderColor: 'rgb(66, 133, 244)',
                borderWidth: 1
            },
            {
                label: 'Aylık Gelir (₺)',
                data: {!! json_encode($chartData['revenue']) !!},
                backgroundColor: 'rgba(52, 168, 83, 0.6)',
                borderColor: 'rgb(52, 168, 83)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush