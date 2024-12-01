@extends('layouts.admin')

@section('title', isset($user) ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            {{ isset($user) ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle' }}
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="container">
                        <form method="POST" 
                              action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}"
                              enctype="multipart/form-data"
                              id="userForm"
                              class="row">
                            @csrf
                            @if(isset($user))
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
                                                    <label class="form-label">Ad Soyad</label>
                                                    <input type="text" 
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           name="name"
                                                           value="{{ old('name', $user->name ?? '') }}"
                                                           required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">E-posta</label>
                                                    <input type="email" 
                                                           class="form-control @error('email') is-invalid @enderror"
                                                           name="email"
                                                           value="{{ old('email', $user->email ?? '') }}"
                                                           required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Şifre {{ isset($user) ? '(Değiştirmek için doldurun)' : '' }}</label>
                                                    <input type="password" 
                                                           class="form-control @error('password') is-invalid @enderror"
                                                           name="password"
                                                           {{ !isset($user) ? 'required' : '' }}>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Şifre Tekrar</label>
                                                    <input type="password" 
                                                           class="form-control"
                                                           name="password_confirmation"
                                                           {{ !isset($user) ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               <!-- Üyelik Bilgileri -->
                               <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Üyelik Bilgileri</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Üyelik Planı</label>
                                                    <select class="form-control" name="plan_id" required>
                                                        @foreach($plans as $plan)
                                                            <option value="{{ $plan->id }}" 
                                                                {{ (old('plan_id', $user->plan_id ?? '') == $plan->id) ? 'selected' : '' }}>
                                                                {{ $plan->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="is_admin" 
                                                           value="1"
                                                           {{ (old('is_admin', $user->is_admin ?? false)) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Yönetici Yetkisi</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Hesap Durumu</label>
                                                    <select class="form-control" name="status">
                                                        <option value="active" {{ (old('status', $user->status ?? '') == 'active') ? 'selected' : '' }}>
                                                            Aktif
                                                        </option>
                                                        <option value="inactive" {{ (old('status', $user->status ?? '') == 'inactive') ? 'selected' : '' }}>
                                                            Pasif
                                                        </option>
                                                        <option value="suspended" {{ (old('status', $user->status ?? '') == 'suspended') ? 'selected' : '' }}>
                                                            Askıya Alınmış
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Mesaj Limiti (Boş: Plan Varsayılanı)</label>
                                                    <input type="number" 
                                                           class="form-control"
                                                           name="custom_response_limit"
                                                           value="{{ old('custom_response_limit', $user->custom_response_limit ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Profil Fotoğrafı -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Profil Fotoğrafı</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            @if(isset($user) && $user->profile_photo)
                                                <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                                                     class="img-fluid rounded-circle" 
                                                     style="width: 150px; height: 150px; object-fit: cover;">
                                            @else
                                                <div class="avatar bg-gradient-primary rounded-circle" 
                                                     style="width: 150px; height: 150px; font-size: 48px; line-height: 150px;">
                                                    {{ isset($user) ? $user->getInitials() : 'YK' }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="custom-file-upload">
                                            <input type="file" 
                                                   name="profile_photo" 
                                                   class="form-control @error('profile_photo') is-invalid @enderror"
                                                   accept="image/*">
                                            <small class="form-text text-muted">
                                                PNG, JPG veya GIF. Maksimum 2MB.
                                            </small>
                                            @error('profile_photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Notlar -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Notlar</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <textarea class="form-control" 
                                                      name="notes" 
                                                      rows="4"
                                                      placeholder="Kullanıcı hakkında notlar...">{{ old('notes', $user->notes ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kaydet Butonu -->
                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light me-2">İptal</a>
                                <button type="submit" class="btn bg-gradient-primary">
                                    {{ isset($user) ? 'Güncelle' : 'Kaydet' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validasyonunu aktifleştir
    initializeFormValidation('userForm');

    // Profil fotoğrafı önizleme
    document.querySelector('input[name="profile_photo"]').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.avatar, .rounded-circle').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endpush