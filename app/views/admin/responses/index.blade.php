@extends('layouts.admin')

@section('title', 'Özel Yanıtlar')

@section('content')
<div class="container-fluid py-4">
    <!-- İstatistik Kartları -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">question_answer</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Toplam Yanıt</p>
                        <h4 class="mb-0">{{ $stats['total_responses'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">{{ $stats['active_responses'] }}</span>
                        aktif yanıt
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Kullanım Sayısı</p>
                        <h4 class="mb-0">{{ $stats['total_usage'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-info text-sm font-weight-bolder">{{ $stats['usage_rate'] }}%</span>
                        kullanım oranı
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">attach_file</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Ekli Dosyalar</p>
                        <h4 class="mb-0">{{ $stats['total_attachments'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-warning text-sm font-weight-bolder">{{ $stats['attachment_size'] }}</span>
                        toplam boyut
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">update</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0">Son Güncelleme</p>
                        <h4 class="mb-0">{{ $stats['last_update']->diffForHumans() }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-danger text-sm font-weight-bolder">{{ $stats['update_frequency'] }}</span>
                        güncelleme sıklığı
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ana Kart -->
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center px-4">
                    <h6 class="text-white text-capitalize mb-0">Özel Yanıtlar Listesi</h6>
                    <a href="{{ route('admin.responses.create') }}" class="btn bg-gradient-dark mb-0">
                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Yeni Yanıt
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body px-0 pb-2">
            <!-- Filtreler -->
            <div class="row mx-4 mb-3">
                <div class="col-md-3">
                    <div class="input-group input-group-static mb-4">
                        <label>Durum</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">Tümü</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-static mb-4">
                        <label>Sıralama</label>
                        <select class="form-control" id="sortFilter">
                            <option value="usage">Kullanım Sayısı</option>
                            <option value="updated">Son Güncelleme</option>
                            <option value="created">Oluşturma</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-outline mb-4">
                        <label class="form-label">Ara...</label>
                        <input type="text" class="form-control" id="searchInput">
                    </div>
                </div>
            </div>

            <!-- Yanıt Listesi -->
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="responsesTable">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Anahtar Kelime</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Yanıt</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanım</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durum</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Son Güncellenme</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
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
                                            @if($response->hasRegexKeyword())
                                                <p class="text-xs text-warning mb-0">Regex</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">
                                        {{ Str::limit($response->response_text, 50) }}
                                    </p>
                                    @if($response->hasAttachments())
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="material-icons text-sm">attach_file</i>
                                            {{ count($response->attachments) }} ek
                                        </p>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-info">
                                        {{ $response->getUsageCount() }}
                                    </span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    @if($response->is_active)
                                        <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{ $response->updated_at->format('d.m.Y H:i') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <a href="#" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.responses.edit', $response->id) }}">
                                                    <i class="material-icons">edit</i> Düzenle
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.responses.show', $response->id) }}">
                                                    <i class="material-icons">visibility</i> Detay
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @if($response->is_active)
                                                <li>
                                                    <form action="{{ route('admin.responses.deactivate', $response->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="material-icons">pause</i> Pasifleştir
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.responses.activate', $response->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="material-icons">play_arrow</i> Aktifleştir
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('admin.responses.destroy', $response->id) }}" method="POST"
                                                      onsubmit="return confirm('Bu yanıtı silmek istediğinizden emin misiniz?')">
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
                        Toplam <b>{{ $responses->total() }}</b> yanıttan 
                        <b>{{ $responses->firstItem() }}-{{ $responses->lastItem() }}</b> arası gösteriliyor
                    </div>
                </div>
                <div class="col-5">
                    {{ $responses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// DataTable initialize
const table = initializeDataTable('responsesTable', {
    order: [[4, 'desc']],
    columnDefs: [
        { orderable: false, targets: [5] }
    ]
});

// Filtreler
document.getElementById('statusFilter').addEventListener('change', updateFilters);
document.getElementById('sortFilter').addEventListener('change', updateFilters);
document.getElementById('searchInput').addEventListener('keyup', debounce(updateFilters, 500));

function updateFilters() {
    const status = document.getElementById('statusFilter').value;
    const sort = document.getElementById('sortFilter').value;
    const search = document.getElementById('searchInput').value;
    
    window.location.href = `${window.location.pathname}?status=${status}&sort=${sort}&search=${search}`;
}
</script>
@endpush