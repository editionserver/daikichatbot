@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="row px-4">
                            <div class="col-6">
                                <h6 class="text-white text-capitalize">Kullanıcı Listesi</h6>
                            </div>
                            <div class="col-6 text-end">
                                <a class="btn bg-gradient-dark mb-0" href="{{ route('admin.users.create') }}">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Yeni Kullanıcı
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pb-2">
                    <!-- Filtreler -->
                    <div class="row px-4 mb-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label>Üyelik Planı</label>
                                <select class="form-control" id="planFilter">
                                    <option value="">Tümü</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-4">
                                <label class="form-label">Ara...</label>
                                <input type="text" class="form-control" id="searchInput">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Plan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Durum</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kayıt Tarihi</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Son Giriş</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($user->profile_photo)
                                                    <img src="{{ asset('storage/'.$user->profile_photo) }}" class="avatar avatar-sm me-3 border-radius-lg">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary border-radius-lg">
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
                                        @if($user->isActive())
                                            <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $user->created_at->format('d.m.Y') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                        <i class="material-icons">edit</i> Düzenle
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                        <i class="material-icons">visibility</i> Detay
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
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

                <!-- Pagination -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-sm">
                                Toplam <b>{{ $users->total() }}</b> kullanıcıdan <b>{{ $users->firstItem() }}-{{ $users->lastItem() }}</b> arası gösteriliyor
                            </div>
                        </div>
                        <div class="col-5">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // DataTable initialize
    const table = initializeDataTable('usersTable', {
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });

    // Filtreler
    document.getElementById('planFilter').addEventListener('change', function() {
        table.column(1).search(this.value).draw();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        table.column(2).search(this.value).draw();
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });
</script>
@endpush