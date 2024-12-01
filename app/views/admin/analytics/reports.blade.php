@extends('layouts.admin')

@section('title', 'Özel Raporlar')

@section('content')
<div class="container-fluid py-4">
    <!-- Rapor Oluşturma -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Özel Rapor Oluştur</h5>
                            <p class="text-sm mb-0">İstediğiniz metrikler ve filtreler ile özel rapor oluşturun</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn bg-gradient-primary mb-2" id="saveTemplate">
                                <i class="material-icons">save</i> Şablon Olarak Kaydet
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="reportForm" method="POST">
                        @csrf
                        <!-- Temel Ayarlar -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-4">
                                    <label class="form-label">Rapor Başlığı</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-4">
                                    <label class="form-label">Açıklama</label>
                                    <input type="text" class="form-control" name="description">
                                </div>
                            </div>
                        </div>

                        <!-- Metrikler -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">Metrikler</h6>
                                <div class="metrics-container">
                                    <div class="row">
                                        @foreach($availableMetrics as $group => $metrics)
                                            <div class="col-md-4">
                                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">{{ $group }}</h6>
                                                @foreach($metrics as $key => $metric)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="metrics[]"
                                                               value="{{ $key }}">
                                                        <label class="custom-control-label">{{ $metric['name'] }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtreler -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">Filtreler</h6>
                                <div class="filters-container">
                                    <div class="filter-item row align-items-center mb-3">
                                        <div class="col-md-3">
                                            <select class="form-control" name="filter_field[]">
                                                <option value="">Filtre Seçin</option>
                                                @foreach($availableFilters as $group => $filters)
                                                    <optgroup label="{{ $group }}">
                                                        @foreach($filters as $key => $filter)
                                                            <option value="{{ $key }}">{{ $filter['name'] }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" name="filter_operator[]">
                                                <option value="equals">Eşit</option>
                                                <option value="not_equals">Eşit Değil</option>
                                                <option value="greater">Büyük</option>
                                                <option value="less">Küçük</option>
                                                <option value="contains">İçerir</option>
                                                <option value="not_contains">İçermez</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="filter_value[]" placeholder="Değer">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-filter">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-info btn-sm" id="addFilter">
                                    <i class="material-icons">add</i> Filtre Ekle
                                </button>
                            </div>
                        </div>

                        <!-- Gruplama ve Sıralama -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="mb-3">Gruplama</h6>
                                <div class="input-group input-group-static">
                                    <select class="form-control" name="group_by">
                                        <option value="">Gruplama Yok</option>
                                        @foreach($groupingOptions as $key => $option)
                                            <option value="{{ $key }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Sıralama</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <select class="form-control" name="sort_field">
                                            <option value="">Alan Seçin</option>
                                            @foreach($sortingOptions as $key => $option)
                                                <option value="{{ $key }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="sort_direction">
                                            <option value="desc">Azalan</option>
                                            <option value="asc">Artan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Görselleştirme -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">Görselleştirme</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-control" name="visualization_type">
                                            <option value="table">Tablo</option>
                                            <option value="line">Çizgi Grafik</option>
                                            <option value="bar">Sütun Grafik</option>
                                            <option value="pie">Pasta Grafik</option>
                                            <option value="area">Alan Grafik</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="show_totals">
                                            <label class="form-check-label">Toplamları Göster</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Seçenekleri -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">Export Seçenekleri</h6>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="export_formats[]" value="pdf">
                                    <label class="form-check-label">PDF</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="export_formats[]" value="excel">
                                    <label class="form-check-label">Excel</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="export_formats[]" value="csv">
                                    <label class="form-check-label">CSV</label>
                                </div>
                            </div>
                        </div>

                        <!-- Planlama -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="mb-3">Rapor Planlaması</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" name="schedule_frequency">
                                            <option value="">Planlama Yok</option>
                                            <option value="daily">Günlük</option>
                                            <option value="weekly">Haftalık</option>
                                            <option value="monthly">Aylık</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 schedule-options d-none">
                                        <select class="form-control" name="schedule_day">
                                            <option value="1">Pazartesi</option>
                                            <option value="2">Salı</option>
                                            <option value="3">Çarşamba</option>
                                            <option value="4">Perşembe</option>
                                            <option value="5">Cuma</option>
                                            <option value="6">Cumartesi</option>
                                            <option value="7">Pazar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 schedule-options d-none">
                                        <input type="time" class="form-control" name="schedule_time">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-light me-2">İptal</button>
                                <button type="button" class="btn btn-info me-2" id="previewReport">Önizleme</button>
                                <button type="submit" class="btn bg-gradient-primary">Rapor Oluştur</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Önceki Raporlar -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Önceki Raporlar</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rapor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Oluşturan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durum</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tarih</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previousReports as $report)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <i class="material-icons text-info">description</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-2">
                                                <h6 class="mb-0 text-sm">{{ $report->title }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $report->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($report->user->profile_photo)
                                                    <img src="{{ asset('storage/'.$report->user->profile_photo) }}" 
                                                         class="avatar avatar-sm me-3">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary">
                                                        {{ $report->user->getInitials() }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $report->user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @switch($report->status)
                                            @case('completed')
                                                <span class="badge badge-sm bg-gradient-success">Tamamlandı</span>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-sm bg-gradient-info">İşleniyor</span>
                                                @break
                                            @case('scheduled')
                                                <span class="badge badge-sm bg-gradient-warning">Planlandı</span>
                                                @break
                                            @default
                                                <span class="badge badge-sm bg-gradient-secondary">{{ $report->status }}</span>
                                                @endswitch
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $report->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.analytics.reports.show', $report->id) }}">
                                                        <i class="material-icons">visibility</i> Görüntüle
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.analytics.reports.download', $report->id) }}">
                                                        <i class="material-icons">download</i> İndir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="duplicateReport({{ $report->id }})">
                                                        <i class="material-icons">content_copy</i> Çoğalt
                                                    </a>
                                                </li>
                                                @if($report->schedule_frequency)
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="editSchedule({{ $report->id }})">
                                                            <i class="material-icons">schedule</i> Planlamayı Düzenle
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.analytics.reports.destroy', $report->id) }}" method="POST"
                                                          onsubmit="return confirm('Bu raporu silmek istediğinizden emin misiniz?')">
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rapor Önizleme Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rapor Önizleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Önizleme içeriği dinamik olarak yüklenecek -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Şablon Kaydetme Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Şablon Olarak Kaydet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-outline mb-4">
                    <label class="form-label">Şablon Adı</label>
                    <input type="text" class="form-control" id="templateName" required>
                </div>
                <div class="input-group input-group-outline mb-4">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" id="templateDescription" rows="3"></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="templatePublic">
                    <label class="custom-control-label">Diğer kullanıcılarla paylaş</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="saveAsTemplate()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Form validasyonu
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const metrics = document.querySelectorAll('input[name="metrics[]"]:checked');
    if (metrics.length === 0) {
        showNotification('En az bir metrik seçmelisiniz', 'warning');
        return;
    }

    // Form verilerini topla
    const formData = new FormData(this);
    
    // Rapor oluştur
    createReport(formData);
});

// Rapor oluşturma
async function createReport(formData) {
    try {
        showLoader();
        
        const response = await fetch('/admin/analytics/reports', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Rapor başarıyla oluşturuldu', 'success');
            window.location.href = `/admin/analytics/reports/${data.report_id}`;
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        showNotification('Rapor oluşturulurken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

// Önizleme
document.getElementById('previewReport').addEventListener('click', async function() {
    const formData = new FormData(document.getElementById('reportForm'));
    formData.append('preview', true);
    
    try {
        showLoader();
        
        const response = await fetch('/admin/analytics/reports/preview', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        document.getElementById('previewContent').innerHTML = data.html;
        
        // Grafikleri yeniden oluştur
        if (data.charts) {
            Object.keys(data.charts).forEach(chartId => {
                const ctx = document.getElementById(chartId).getContext('2d');
                new Chart(ctx, data.charts[chartId]);
            });
        }
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    } catch (error) {
        showNotification('Önizleme oluşturulurken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
});

// Filtre ekle/kaldır
document.getElementById('addFilter').addEventListener('click', function() {
    const template = document.querySelector('.filter-item').cloneNode(true);
    template.querySelector('input').value = '';
    document.querySelector('.filters-container').appendChild(template);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-filter')) {
        const filters = document.querySelectorAll('.filter-item');
        if (filters.length > 1) {
            e.target.closest('.filter-item').remove();
        } else {
            showNotification('En az bir filtre olmalıdır', 'warning');
        }
    }
});

// Planlama seçenekleri
document.querySelector('select[name="schedule_frequency"]').addEventListener('change', function(e) {
    const options = document.querySelectorAll('.schedule-options');
    if (e.target.value) {
        options.forEach(option => option.classList.remove('d-none'));
    } else {
        options.forEach(option => option.classList.add('d-none'));
    }
});

// Şablon olarak kaydet
document.getElementById('saveTemplate').addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
});

async function saveAsTemplate() {
    const formData = new FormData(document.getElementById('reportForm'));
    formData.append('template_name', document.getElementById('templateName').value);
    formData.append('template_description', document.getElementById('templateDescription').value);
    formData.append('template_public', document.getElementById('templatePublic').checked);
    
    try {
        const response = await fetch('/admin/analytics/reports/templates', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Şablon başarıyla kaydedildi', 'success');
            bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        showNotification('Şablon kaydedilirken bir hata oluştu', 'error');
    }
}

// Rapor çoğaltma
async function duplicateReport(reportId) {
    try {
        const response = await fetch(`/admin/analytics/reports/${reportId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        showNotification('Rapor çoğaltılırken bir hata oluştu', 'error');
    }
}

// Görselleştirme seçenekleri
document.querySelector('select[name="visualization_type"]').addEventListener('change', updateVisualizationOptions);

function updateVisualizationOptions(e) {
    const type = e.target.value;
    const totalCheck = document.querySelector('input[name="show_totals"]').closest('.form-check');
    
    if (type === 'table') {
        totalCheck.classList.remove('d-none');
    } else {
        totalCheck.classList.add('d-none');
    }
}
</script>
@endpush