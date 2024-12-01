@extends('layouts.admin')

@section('title', isset($plan) ? 'Plan Düzenle' : 'Yeni Plan')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            {{ isset($plan) ? 'Plan Düzenle: '.$plan->name : 'Yeni Plan Oluştur' }}
                        </h6>
                    </div>
                </div>

                <div class="card-body mt-2">
                    <form method="POST" 
                          action="{{ isset($plan) ? route('admin.plans.update', $plan->id) : route('admin.plans.store') }}"
                          id="planForm"
                          class="row">
                        @csrf
                        @if(isset($plan))
                            @method('PUT')
                        @endif

                        <div class="col-md-8">
                            <!-- Temel Bilgiler -->
                            <div class="card">
                                <div class="card-header">
                                <h5 class="mb-0">Temel Bilgiler</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-4">
                                                <label class="form-label">Plan Adı</label>
                                                <input type="text" 
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       name="name"
                                                       value="{{ old('name', $plan->name ?? '') }}"
                                                       required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-4">
                                                <label class="form-label">Aylık Fiyat (₺)</label>
                                                <input type="number" 
                                                       step="0.01"
                                                       class="form-control @error('monthly_price') is-invalid @enderror"
                                                       name="monthly_price"
                                                       value="{{ old('monthly_price', $plan->monthly_price ?? '') }}"
                                                       required>
                                                @error('monthly_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-4">
                                                <label class="form-label">Mesaj Limiti (-1: Sınırsız)</label>
                                                <input type="number" 
                                                       class="form-control @error('response_limit') is-invalid @enderror"
                                                       name="response_limit"
                                                       value="{{ old('response_limit', $plan->response_limit ?? '') }}"
                                                       required>
                                                @error('response_limit')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-4">
                                                <label class="form-label">Deneme Süresi (Gün)</label>
                                                <input type="number" 
                                                       class="form-control @error('trial_days') is-invalid @enderror"
                                                       name="trial_days"
                                                       value="{{ old('trial_days', $plan->trial_days ?? 0) }}">
                                                @error('trial_days')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Plan Açıklaması</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  name="description"
                                                  rows="4">{{ old('description', $plan->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Özellikler -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Plan Özellikleri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="features-container">
                                        @if(isset($plan) && $plan->features)
                                            @foreach($plan->features as $feature)
                                                <div class="feature-item row mb-3">
                                                    <div class="col-md-5">
                                                        <div class="input-group input-group-outline">
                                                            <input type="text" 
                                                                   class="form-control"
                                                                   name="features[]"
                                                                   value="{{ $feature }}"
                                                                   placeholder="Özellik">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm remove-feature">
                                                            <i class="material-icons">delete</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="feature-item row mb-3">
                                            <div class="col-md-5">
                                                <div class="input-group input-group-outline">
                                                    <input type="text" 
                                                           class="form-control"
                                                           name="features[]"
                                                           placeholder="Yeni özellik">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm remove-feature">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mt-3" id="addFeature">
                                        <i class="material-icons">add</i> Özellik Ekle
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Plan Ayarları -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Plan Ayarları</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_active" 
                                               value="1"
                                               {{ (old('is_active', $plan->is_active ?? true)) ? 'checked' : '' }}>
                                        <label class="custom-control-label">Aktif Plan</label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_popular" 
                                               value="1"
                                               {{ (old('is_popular', $plan->is_popular ?? false)) ? 'checked' : '' }}>
                                        <label class="custom-control-label">Popüler Plan</label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_recommended" 
                                               value="1"
                                               {{ (old('is_recommended', $plan->is_recommended ?? false)) ? 'checked' : '' }}>
                                        <label class="custom-control-label">Önerilen Plan</label>
                                    </div>

                                    <hr>

                                    <div class="form-group mb-3">
                                        <label>Plan Rengi</label>
                                        <select class="form-control" name="color">
                                            <option value="primary" {{ (old('color', $plan->color ?? '') == 'primary') ? 'selected' : '' }}>Mavi</option>
                                            <option value="secondary" {{ (old('color', $plan->color ?? '') == 'secondary') ? 'selected' : '' }}>Gri</option>
                                            <option value="success" {{ (old('color', $plan->color ?? '') == 'success') ? 'selected' : '' }}>Yeşil</option>
                                            <option value="danger" {{ (old('color', $plan->color ?? '') == 'danger') ? 'selected' : '' }}>Kırmızı</option>
                                            <option value="warning" {{ (old('color', $plan->color ?? '') == 'warning') ? 'selected' : '' }}>Sarı</option>
                                            <option value="info" {{ (old('color', $plan->color ?? '') == 'info') ? 'selected' : '' }}>Açık Mavi</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Plan Sırası</label>
                                        <input type="number" 
                                               class="form-control"
                                               name="sort_order"
                                               value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
                                        <small class="form-text text-muted">
                                            Düşük sayı daha önce gösterilir.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Plan İstatistikleri -->
                            @if(isset($plan))
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Plan İstatistikleri</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Toplam Kullanıcı:</strong> {{ $plan->users_count }}</p>
                                    <p><strong>Aktif Kullanıcı:</strong> {{ $plan->active_users_count }}</p>
                                    <p><strong>Aylık Gelir:</strong> {{ number_format($plan->monthly_revenue, 2) }} ₺</p>
                                    <p><strong>Ortalama Kullanım:</strong> {{ $plan->average_usage }} mesaj/kullanıcı</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Kaydet Butonu -->
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-light me-2">İptal</a>
                            <button type="submit" class="btn bg-gradient-primary">
                                {{ isset($plan) ? 'Güncelle' : 'Kaydet' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Plan özellikleri yönetimi
document.getElementById('addFeature').addEventListener('click', function() {
    const container = document.querySelector('.features-container');
    const template = document.querySelector('.feature-item').cloneNode(true);
    template.querySelector('input').value = '';
    container.appendChild(template);
    
    // Yeni eklenen özellik için silme butonunu aktifleştir
    template.querySelector('.remove-feature').addEventListener('click', removeFeature);
});

// Mevcut özellikler için silme butonlarını aktifleştir
document.querySelectorAll('.remove-feature').forEach(button => {
    button.addEventListener('click', removeFeature);
});

function removeFeature() {
    const featureItems = document.querySelectorAll('.feature-item');
    if (featureItems.length > 1) {
        this.closest('.feature-item').remove();
    } else {
        this.closest('.feature-item').querySelector('input').value = '';
    }
}

// Form validasyonu
document.getElementById('planForm').addEventListener('submit', function(e) {
    const monthlyPrice = document.querySelector('input[name="monthly_price"]').value;
    const responseLimit = document.querySelector('input[name="response_limit"]').value;
    
    if (monthlyPrice < 0) {
        e.preventDefault();
        showNotification('Aylık fiyat negatif olamaz', 'error');
    }
    
    if (responseLimit < -1) {
        e.preventDefault();
        showNotification('Geçersiz mesaj limiti', 'error');
    }
});
</script>
@endpush