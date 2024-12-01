@extends('layouts.admin')

@section('title', $plan->name . ' Plan Detayı')

@section('content')
<div class="container-fluid py-4">
    <!-- Üst Bilgi Kartları -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Aktif Kullanıcı</p>
                        <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
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
                        geçen aya göre
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">payments</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Aylık Gelir</p>
                        <h4 class="mb-0">{{ number_format($stats['monthly_revenue'], 2) }} ₺</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">{{ $stats['average_revenue_per_user'] }} ₺ </span>
                        kullanıcı başına
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">update</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Ortalama Kullanım</p>
                        <h4 class="mb-0">{{ $stats['average_messages'] }} mesaj</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-info text-sm font-weight-bolder">{{ $stats['messages_per_day'] }}</span>
                        günlük ortalama
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">timer</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Ortalama Üyelik Süresi</p>
                        <h4 class="mb-0">{{ $stats['average_subscription_duration'] }} gün</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-warning text-sm font-weight-bolder">%{{ $stats['retention_rate'] }}</span>
                        elde tutma oranı
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Plan Detayları -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Plan Detayları</h5>
                        <div>
                            <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-info mb-0">
                                <i class="material-icons text-sm">edit</i> Düzenle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="row align-items-center mb-3">
                        <div class="col-9">
                            <h6 class="mb-0">Plan Durumu</h6>
                        </div>
                        <div class="col-3 text-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       {{ $plan->is_active ? 'checked' : '' }}
                                       onclick="togglePlanStatus({{ $plan->id }})">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col">
                            <p class="text-sm mb-1">Aylık Fiyat</p>
                            <h6 class="mb-0">{{ number_format($plan->monthly_price, 2) }} ₺</h6>
                        </div>
                        <div class="col">
                            <p class="text-sm mb-1">Mesaj Limiti</p>
                            <h6 class="mb-0">{{ $plan->response_limit === -1 ? 'Sınırsız' : $plan->response_limit }}</h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <p class="text-sm mb-1">Deneme Süresi</p>
                            <h6 class="mb-0">{{ $plan->trial_days }} gün</h6>
                        </div>
                        <div class="col">
                            <p class="text-sm mb-1">Oluşturulma</p>
                            <h6 class="mb-0">{{ $plan->created_at->format('d.m.Y') }}</h6>
                        </div>
                    </div>

                    <hr class="horizontal dark">

                    <h6 class="mb-3">Özellikler</h6>
                    <ul class="list-group">
                        @foreach($plan->features as $feature)
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                            <i class="material-icons text-success text-sm">check</i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Kullanım Grafiği -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Kullanım İstatistikleri</h5>
                    <div class="btn-group mt-3">
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
                <div class="card-body pt-3">
                    <div class="chart">
                        <canvas id="usageChart" class="chart-canvas" height="375"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alt Kartlar -->
    <div class="row mt-4">
        <!-- Kullanıcı Listesi -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Plan Kullanıcıları</h5>
                        <a href="{{ route('admin.users.index', ['plan' => $plan->id]) }}" class="btn btn-sm btn-link mb-0">
                            Tümünü Gör
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kayıt</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mesaj</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Son Aktivite</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
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
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $user->created_at->format('d.m.Y') }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ $user->messages_count }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : '-' }}
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

        <!-- Popüler Özellikler -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Popüler Özellikler</h5>
                </div>
                <div class="card-body">
                    @foreach($popularFeatures as $feature)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $feature['name'] }}</h6>
                            <p class="text-xs text-secondary mb-0">
                                {{ $feature['usage_count'] }} kullanım
                            </p>
                        </div>
                        <div class="ms-auto">
                            <div class="progress" style="width: 100px">
                                <div class="progress-bar bg-gradient-info" 
                                     role="progressbar" 
                                     style="width: {{ $feature['usage_percentage'] }}%" 
                                     aria-valuenow="{{ $feature['usage_percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Kullanım grafiği
var ctx = document.getElementById('usageChart').getContext('2d');
var usageChart;

function updateChart(period = 'daily') {
    fetch(`/admin/plans/{{ $plan->id }}/usage-stats?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (usageChart) {
                usageChart.destroy();
            }

            usageChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Kullanıcı Sayısı',
                            data: data.users,
                            borderColor: '#4CAF50',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: 'rgba(