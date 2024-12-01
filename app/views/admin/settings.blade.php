@extends('layouts.admin')

@section('title', 'Sistem Ayarları')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sol Menü -->
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Ayarlar Menüsü</h6>
                </div>
                <div class="card-body">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#general" role="tab">
                            <i class="material-icons me-2">settings</i>
                            Genel Ayarlar
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#chatbot" role="tab">
                            <i class="material-icons me-2">chat</i>
                            Chatbot Ayarları
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#api" role="tab">
                            <i class="material-icons me-2">code</i>
                            API Ayarları
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#email" role="tab">
                            <i class="material-icons me-2">email</i>
                            E-posta Ayarları
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#notifications" role="tab">
                            <i class="material-icons me-2">notifications</i>
                            Bildirim Ayarları
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#security" role="tab">
                            <i class="material-icons me-2">security</i>
                            Güvenlik Ayarları
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#backup" role="tab">
                            <i class="material-icons me-2">backup</i>
                            Yedekleme Ayarları
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ İçerik -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="mb-0">Sistem Ayarları</h5>
                        </div>
                        <div class="col-4 text-end">
                            <button type="button" class="btn btn-sm bg-gradient-info mb-0" onclick="checkSystemRequirements()">
                                <i class="material-icons">check_circle</i>
                                Sistem Kontrolü
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="tab-content">
                            <!-- Genel Ayarlar Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Temel Bilgiler</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Site Adı</label>
                                                            <input type="text" 
                                                                   class="form-control @error('site_name') is-invalid @enderror" 
                                                                   name="site_name" 
                                                                   value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                                                                   required>
                                                            @error('site_name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Site URL</label>
                                                            <input type="url" 
                                                                   class="form-control @error('site_url') is-invalid @enderror" 
                                                                   name="site_url" 
                                                                   value="{{ old('site_url', $settings['site_url'] ?? '') }}"
                                                                   required>
                                                            @error('site_url')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Admin E-posta</label>
                                                            <input type="email" 
                                                                   class="form-control @error('admin_email') is-invalid @enderror" 
                                                                   name="admin_email" 
                                                                   value="{{ old('admin_email', $settings['admin_email'] ?? '') }}"
                                                                   required>
                                                            @error('admin_email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group input-group-static mb-4">
                                                            <label>Zaman Dilimi</label>
                                                            <select class="form-control" name="timezone" required>
                                                                @foreach($timezones as $tz)
                                                                    <option value="{{ $tz }}" 
                                                                            {{ (old('timezone', $settings['timezone'] ?? '') == $tz) ? 'selected' : '' }}>
                                                                        {{ $tz }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logo ve Favicon Bölümü -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Logo ve Favicon</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-4">
                                                            <label class="form-label d-block">Site Logo</label>
                                                            @if(isset($settings['logo']) && $settings['logo'])
                                                                <img src="{{ asset('storage/'.$settings['logo']) }}" 
                                                                     alt="Site Logo" 
                                                                     class="img-fluid mb-2" 
                                                                     style="max-height: 100px">
                                                            @endif
                                                            <input type="file" 
                                                                   class="form-control @error('logo') is-invalid @enderror" 
                                                                   name="logo"
                                                                   accept="image/*">
                                                            <small class="form-text text-muted">
                                                                Önerilen boyut: 200x50px, maksimum 2MB
                                                            </small>
                                                            @error('logo')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-4">
                                                            <label class="form-label d-block">Favicon</label>
                                                            @if(isset($settings['favicon']) && $settings['favicon'])
                                                                <img src="{{ asset('storage/'.$settings['favicon']) }}" 
                                                                     alt="Favicon" 
                                                                     class="img-fluid mb-2" 
                                                                     style="max-height: 32px">
                                                            @endif
                                                            <input type="file" 
                                                                   class="form-control @error('favicon') is-invalid @enderror" 
                                                                   name="favicon"
                                                                   accept="image/x-icon,image/png">
                                                            <small class="form-text text-muted">
                                                                Önerilen boyut: 32x32px, .ico veya .png formatı
                                                            </small>
                                                            @error('favicon')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Genel Özellikler Bölümü -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Genel Özellikler</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="maintenance_mode" 
                                                                   value="1" 
                                                                   {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label">Bakım Modu</label>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="enable_registration" 
                                                                   value="1" 
                                                                   {{ old('enable_registration', $settings['enable_registration'] ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label">Kayıt Olmayı Etkinleştir</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="show_cookie_warning" 
                                                                   value="1" 
                                                                   {{ old('show_cookie_warning', $settings['show_cookie_warning'] ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label">Çerez Uyarısı Göster</label>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="auto_update" 
                                                                   value="1" 
                                                                   {{ old('auto_update', $settings['auto_update'] ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label">Otomatik Güncelleme</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chatbot Ayarları Tab -->
                            <div class="tab-pane fade" id="chatbot" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">OpenAI Ayarları</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">OpenAI API Anahtarı</label>
                                                    <input type="password" 
                                                           class="form-control @error('openai_api_key') is-invalid @enderror" 
                                                           name="openai_api_key" 
                                                           value="{{ old('openai_api_key', $settings['openai_api_key'] ?? '') }}"
                                                           required>
                                                    @error('openai_api_key')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Model</label>
                                                    <select class="form-control" name="openai_model" required>
                                                        <option value="gpt-4" {{ (old('openai_model', $settings['openai_model'] ?? '') == 'gpt-4') ? 'selected' : '' }}>GPT-4</option>
                                                        <option value="gpt-3.5-turbo" {{ (old('openai_model', $settings['openai_model'] ?? '') == 'gpt-3.5-turbo') ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                                                        <option value="gpt-3.5-turbo-16k" {{ (old('openai_model', $settings['openai_model'] ?? '') == 'gpt-3.5-turbo-16k') ? 'selected' : '' }}>GPT-3.5 Turbo 16K</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Maksimum Token</label>
                                                    <input type="number" 
                                                           class="form-control @error('max_tokens') is-invalid @enderror" 
                                                           name="max_tokens" 
                                                           value="{{ old('max_tokens', $settings['max_tokens'] ?? 2000) }}"
                                                           min="1"
                                                           max="32000"
                                                           required>
                                                    @error('max_tokens')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Sıcaklık (Temperature)</label>
                                                    <input type="number" 
                                                           class="form-control @error('temperature') is-invalid @enderror" 
                                                           name="temperature" 
                                                           value="{{ old('temperature', $settings['temperature'] ?? 0.7) }}"
                                                           min="0"
                                                           max="2"
                                                           step="0.1"
                                                           required>
                                                    @error('temperature')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Presence Penalty</label>
                                                    <input type="number" 
                                                           class="form-control @error('presence_penalty') is-invalid @enderror" 
                                                           name="presence_penalty" 
                                                           value="{{ old('presence_penalty', $settings['presence_penalty'] ?? 0) }}"
                                                           min="-2"
                                                           max="2"
                                                           step="0.1"
                                                           required>
                                                    @error('presence_penalty')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label>Sistem Mesajı</label>
                                                    <textarea class="form-control @error('system_message') is-invalid @enderror" 
                                                              name="system_message" 
                                                              rows="4"
                                                              required>{{ old('system_message', $settings['system_message'] ?? '') }}</textarea>
                                                    @error('system_message')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">
                                                    Chatbot'un nasıl davranması gerektiğini belirleyen sistem mesajı.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="stream_responses" 
                                                           value="1" 
                                                           {{ old('stream_responses', $settings['stream_responses'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Stream Yanıtlar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="save_conversations" 
                                                           value="1" 
                                                           {{ old('save_conversations', $settings['save_conversations'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Sohbetleri Kaydet</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kullanım Limitleri -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Kullanım Limitleri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Misafir Kullanıcı Limiti</label>
                                                    <input type="number" 
                                                           class="form-control @error('guest_limit') is-invalid @enderror" 
                                                           name="guest_limit" 
                                                           value="{{ old('guest_limit', $settings['guest_limit'] ?? 2) }}"
                                                           min="0"
                                                           required>
                                                    @error('guest_limit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Ücretsiz Plan Limiti</label>
                                                    <input type="number" 
                                                           class="form-control @error('free_plan_limit') is-invalid @enderror" 
                                                           name="free_plan_limit" 
                                                           value="{{ old('free_plan_limit', $settings['free_plan_limit'] ?? 50) }}"
                                                           min="0"
                                                           required>
                                                    @error('free_plan_limit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="limit_conversations" 
                                                           value="1" 
                                                           {{ old('limit_conversations', $settings['limit_conversations'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Sohbet Sayısını Limitle</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="limit_tokens" 
                                                           value="1" 
                                                           {{ old('limit_tokens', $settings['limit_tokens'] ?? false) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Token Sayısını Limitle</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- API Ayarları Tab -->
                            <div class="tab-pane fade" id="api" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">API Yapılandırması</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="enable_api" 
                                                           value="1" 
                                                           {{ old('enable_api', $settings['enable_api'] ?? false) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">API'yi Etkinleştir</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Rate Limit (dk)</label>
                                                    <input type="number" 
                                                           class="form-control @error('api_rate_limit') is-invalid @enderror" 
                                                           name="api_rate_limit" 
                                                           value="{{ old('api_rate_limit', $settings['api_rate_limit'] ?? 60) }}"
                                                           min="1"
                                                           required>
                                                    @error('api_rate_limit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Token Geçerlilik (gün)</label>
                                                    <input type="number" 
                                                           class="form-control @error('api_token_expiry') is-invalid @enderror" 
                                                           name="api_token_expiry" 
                                                           value="{{ old('api_token_expiry', $settings['api_token_expiry'] ?? 30) }}"
                                                           min="1"
                                                           required>
                                                    @error('api_token_expiry')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label>İzin Verilen IP'ler</label>
                                                    <textarea class="form-control @error('api_allowed_ips') is-invalid @enderror" 
                                                              name="api_allowed_ips" 
                                                              rows="4"
                                                              placeholder="Her satıra bir IP adresi">{{ old('api_allowed_ips', $settings['api_allowed_ips'] ?? '') }}</textarea>
                                                    @error('api_allowed_ips')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">
                                                    Boş bırakılırsa tüm IP'lere izin verilir.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- API Belgelendirme -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">API Belgelendirme</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="enable_api_docs" 
                                                           value="1" 
                                                           {{ old('enable_api_docs', $settings['enable_api_docs'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">API Belgelerini Etkinleştir</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">API Başlığı</label>
                                                    <input type="text" 
                                                           class="form-control @error('api_title') is-invalid @enderror" 
                                                           name="api_title" 
                                                           value="{{ old('api_title', $settings['api_title'] ?? 'Daikin Chatbot API') }}"
                                                           required>
                                                    @error('api_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label>API Açıklaması</label>
                                                    <textarea class="form-control @error('api_description') is-invalid @enderror" 
                                                              name="api_description" 
                                                              rows="4">{{ old('api_description', $settings['api_description'] ?? '') }}</textarea>
                                                    @error('api_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- E-posta Ayarları Tab -->
                            <div class="tab-pane fade" id="email" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">SMTP Yapılandırması</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">SMTP Sunucu</label>
                                                    <input type="text" 
                                                           class="form-control @error('smtp_host') is-invalid @enderror" 
                                                           name="smtp_host" 
                                                           value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                                           required>
                                                    @error('smtp_host')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">SMTP Port</label>
                                                    <input type="number" 
                                                           class="form-control @error('smtp_port') is-invalid @enderror" 
                                                           name="smtp_port" 
                                                           value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                                                           required>
                                                    @error('smtp_port')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">SMTP Kullanıcı</label>
                                                    <input type="text" 
                                                           class="form-control @error('smtp_username') is-invalid @enderror" 
                                                           name="smtp_username" 
                                                           value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                                           required>
                                                    @error('smtp_username')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">SMTP Şifre</label>
                                                    <input type="password" 
                                                           class="form-control @error('smtp_password') is-invalid @enderror" 
                                                           name="smtp_password" 
                                                           value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                                           required>
                                                    @error('smtp_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Gönderen Adı</label>
                                                    <input type="text" 
                                                           class="form-control @error('mail_from_name') is-invalid @enderror" 
                                                           name="mail_from_name" 
                                                           value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                                                           required>
                                                    @error('mail_from_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Gönderen E-posta</label>
                                                    <input type="email" 
                                                           class="form-control @error('mail_from_address') is-invalid @enderror" 
                                                           name="mail_from_address" 
                                                           value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                                           required>
                                                    @error('mail_from_address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="smtp_encryption" 
                                                           value="tls"
                                                           {{ (old('smtp_encryption', $settings['smtp_encryption'] ?? 'tls') == 'tls') ? 'checked' : '' }}>
                                                    <label class="custom-control-label">TLS Kullan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" 
                                                        class="btn btn-info"
                                                        onclick="testEmailSettings()">
                                                    Test E-postası Gönder
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- E-posta Şablonları -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">E-posta Şablonları</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="nav-wrapper position-relative end-0">
                                                    <ul class="nav nav-pills nav-fill flex-row p-1" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link mb-0 px-0 py-1 active" 
                                                               data-bs-toggle="tab" 
                                                               href="#welcome-email" 
                                                               role="tab" 
                                                               aria-selected="true">
                                                                Hoşgeldiniz
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link mb-0 px-0 py-1" 
                                                               data-bs-toggle="tab" 
                                                               href="#reset-password" 
                                                               role="tab" 
                                                               aria-selected="false">
                                                                Şifre Sıfırlama
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link mb-0 px-0 py-1" 
                                                               data-bs-toggle="tab" 
                                                               href="#verification" 
                                                               role="tab" 
                                                               aria-selected="false">
                                                                E-posta Doğrulama
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <!-- Hoşgeldiniz E-postası -->
                                            <div class="tab-pane fade show active" id="welcome-email" role="tabpanel">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Konu</label>
                                                            <input type="text" 
                                                                   class="form-control @error('welcome_email_subject') is-invalid @enderror" 
                                                                   name="welcome_email_subject" 
                                                                   value="{{ old('welcome_email_subject', $settings['welcome_email_subject'] ?? 'Hoş Geldiniz') }}"
                                                                   required>
                                                            @error('welcome_email_subject')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label>İçerik</label>
                                                            <textarea class="form-control @error('welcome_email_content') is-invalid @enderror" 
                                                                      name="welcome_email_content" 
                                                                      rows="10"
                                                                      required>{{ old('welcome_email_content', $settings['welcome_email_content'] ?? '') }}</textarea>
                                                            @error('welcome_email_content')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Kullanılabilir değişkenler: {name}, {email}, {login_url}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Şifre Sıfırlama E-postası -->
                                            <div class="tab-pane fade" id="reset-password" role="tabpanel">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Konu</label>
                                                            <input type="text" 
                                                                   class="form-control @error('reset_password_subject') is-invalid @enderror" 
                                                                   name="reset_password_subject" 
                                                                   value="{{ old('reset_password_subject', $settings['reset_password_subject'] ?? 'Şifre Sıfırlama') }}"
                                                                   required>
                                                            @error('reset_password_subject')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label>İçerik</label>
                                                            <textarea class="form-control @error('reset_password_content') is-invalid @enderror" 
                                                                      name="reset_password_content" 
                                                                      rows="10"
                                                                      required>{{ old('reset_password_content', $settings['reset_password_content'] ?? '') }}</textarea>
                                                            @error('reset_password_content')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Kullanılabilir değişkenler: {name}, {email}, {reset_url}, {expiry_hours}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- E-posta Doğrulama E-postası -->
                                            <div class="tab-pane fade" id="verification" role="tabpanel">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label class="form-label">Konu</label>
                                                            <input type="text" 
                                                                   class="form-control @error('verification_email_subject') is-invalid @enderror" 
                                                                   name="verification_email_subject" 
                                                                   value="{{ old('verification_email_subject', $settings['verification_email_subject'] ?? 'E-posta Doğrulama') }}"
                                                                   required>
                                                            @error('verification_email_subject')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group input-group-outline mb-4">
                                                            <label>İçerik</label>
                                                            <textarea class="form-control @error('verification_email_content') is-invalid @enderror" 
                                                                      name="verification_email_content" 
                                                                      rows="10"
                                                                      required>{{ old('verification_email_content', $settings['verification_email_content'] ?? '') }}</textarea>
                                                            @error('verification_email_content')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            Kullanılabilir değişkenler: {name}, {email}, {verification_url}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bildirim Ayarları Tab -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">E-posta Bildirimleri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input"
                                                    type="checkbox" 
                                                           name="notify_new_user" 
                                                           value="1" 
                                                           {{ old('notify_new_user', $settings['notify_new_user'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Yeni Kullanıcı Kaydı</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_failed_login" 
                                                           value="1" 
                                                           {{ old('notify_failed_login', $settings['notify_failed_login'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Başarısız Giriş Denemeleri</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_api_error" 
                                                           value="1" 
                                                           {{ old('notify_api_error', $settings['notify_api_error'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">API Hataları</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_low_credits" 
                                                           value="1" 
                                                           {{ old('notify_low_credits', $settings['notify_low_credits'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Düşük Kredi Uyarısı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Uygulama İçi Bildirimler -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Uygulama İçi Bildirimler</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_new_message" 
                                                           value="1" 
                                                           {{ old('notify_new_message', $settings['notify_new_message'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Yeni Mesaj Bildirimleri</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_response_limit" 
                                                           value="1" 
                                                           {{ old('notify_response_limit', $settings['notify_response_limit'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Yanıt Limiti Uyarıları</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="notify_system_updates" 
                                                           value="1" 
                                                           {{ old('notify_system_updates', $settings['notify_system_updates'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Sistem Güncellemeleri</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bildirim Kanalları -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Bildirim Kanalları</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Slack Webhook URL</label>
                                                    <input type="url" 
                                                           class="form-control @error('slack_webhook_url') is-invalid @enderror" 
                                                           name="slack_webhook_url" 
                                                           value="{{ old('slack_webhook_url', $settings['slack_webhook_url'] ?? '') }}">
                                                    @error('slack_webhook_url')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Discord Webhook URL</label>
                                                    <input type="url" 
                                                           class="form-control @error('discord_webhook_url') is-invalid @enderror" 
                                                           name="discord_webhook_url" 
                                                           value="{{ old('discord_webhook_url', $settings['discord_webhook_url'] ?? '') }}">
                                                    @error('discord_webhook_url')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Telegram Bot Token</label>
                                                    <input type="text" 
                                                           class="form-control @error('telegram_bot_token') is-invalid @enderror" 
                                                           name="telegram_bot_token" 
                                                           value="{{ old('telegram_bot_token', $settings['telegram_bot_token'] ?? '') }}">
                                                    @error('telegram_bot_token')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Telegram Chat ID</label>
                                                    <input type="text" 
                                                           class="form-control @error('telegram_chat_id') is-invalid @enderror" 
                                                           name="telegram_chat_id" 
                                                           value="{{ old('telegram_chat_id', $settings['telegram_chat_id'] ?? '') }}">
                                                    @error('telegram_chat_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" 
                                                        class="btn btn-info"
                                                        onclick="testNotificationChannels()">
                                                    Kanalları Test Et
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güvenlik Ayarları Tab -->
                            <div class="tab-pane fade" id="security" role="tabpanel">
                                <!-- Şifre Politikası -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Şifre Politikası</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Minimum Şifre Uzunluğu</label>
                                                    <input type="number" 
                                                           class="form-control @error('min_password_length') is-invalid @enderror" 
                                                           name="min_password_length" 
                                                           value="{{ old('min_password_length', $settings['min_password_length'] ?? 8) }}"
                                                           min="6"
                                                           required>
                                                    @error('min_password_length')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Şifre Son Kullanma (gün)</label>
                                                    <input type="number" 
                                                           class="form-control @error('password_expiry_days') is-invalid @enderror" 
                                                           name="password_expiry_days" 
                                                           value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? 90) }}"
                                                           min="0">
                                                    @error('password_expiry_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="require_uppercase" 
                                                           value="1" 
                                                           {{ old('require_uppercase', $settings['require_uppercase'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Büyük Harf Zorunlu</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="require_numeric" 
                                                           value="1" 
                                                           {{ old('require_numeric', $settings['require_numeric'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Sayı Zorunlu</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="require_special_char" 
                                                           value="1" 
                                                           {{ old('require_special_char', $settings['require_special_char'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Özel Karakter Zorunlu</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Oturum Güvenliği -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Oturum Güvenliği</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Maksimum Giriş Denemesi</label>
                                                    <input type="number" 
                                                           class="form-control @error('max_login_attempts') is-invalid @enderror" 
                                                           name="max_login_attempts" 
                                                           value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                                                           min="1"
                                                           required>
                                                    @error('max_login_attempts')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Hesap Kilitleme Süresi (dk)</label>
                                                    <input type="number" 
                                                           class="form-control @error('account_lockout_duration') is-invalid @enderror" 
                                                           name="account_lockout_duration" 
                                                           value="{{ old('account_lockout_duration', $settings['account_lockout_duration'] ?? 30) }}"
                                                           min="1"
                                                           required>
                                                    @error('account_lockout_duration')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="enable_2fa" 
                                                           value="1" 
                                                           {{ old('enable_2fa', $settings['enable_2fa'] ?? false) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">İki Faktörlü Doğrulama</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="force_ssl" 
                                                           value="1" 
                                                           {{ old('force_ssl', $settings['force_ssl'] ?? true) ? 'checked' : '' }}>
                                                           <label class="custom-control-label">SSL Zorunlu</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Oturum Süresi (dk)</label>
                                                    <input type="number" 
                                                           class="form-control @error('session_lifetime') is-invalid @enderror" 
                                                           name="session_lifetime" 
                                                           value="{{ old('session_lifetime', $settings['session_lifetime'] ?? 120) }}"
                                                           min="1"
                                                           required>
                                                    @error('session_lifetime')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Yedekleme Ayarları Tab -->
                            <div class="tab-pane fade" id="backup" role="tabpanel">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Yedekleme Yapılandırması</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="enable_auto_backup" 
                                                           value="1" 
                                                           {{ old('enable_auto_backup', $settings['enable_auto_backup'] ?? false) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Otomatik Yedekleme</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Yedekleme Sıklığı</label>
                                                    <select class="form-control" name="backup_frequency">
                                                        <option value="daily" {{ (old('backup_frequency', $settings['backup_frequency'] ?? '') == 'daily') ? 'selected' : '' }}>Günlük</option>
                                                        <option value="weekly" {{ (old('backup_frequency', $settings['backup_frequency'] ?? '') == 'weekly') ? 'selected' : '' }}>Haftalık</option>
                                                        <option value="monthly" {{ (old('backup_frequency', $settings['backup_frequency'] ?? '') == 'monthly') ? 'selected' : '' }}>Aylık</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Yedekleme Hedefleri</h6>
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="backup_database" 
                                                           value="1" 
                                                           {{ old('backup_database', $settings['backup_database'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Veritabanı</label>
                                                </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="backup_files" 
                                                           value="1" 
                                                           {{ old('backup_files', $settings['backup_files'] ?? true) ? 'checked' : '' }}>
                                                    <label class="custom-control-label">Dosya Sistemi</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Depolama Ayarları</h6>
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Yedek Saklama Süresi (gün)</label>
                                                    <input type="number" 
                                                           class="form-control @error('backup_retention_days') is-invalid @enderror" 
                                                           name="backup_retention_days" 
                                                           value="{{ old('backup_retention_days', $settings['backup_retention_days'] ?? 30) }}"
                                                           min="1"
                                                           required>
                                                    @error('backup_retention_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" 
                                                        class="btn bg-gradient-info"
                                                        onclick="createBackup()">
                                                    <i class="material-icons">backup</i>
                                                    Manuel Yedek Al
                                                </button>

                                                <button type="button" 
                                                        class="btn btn-primary"
                                                        onclick="showBackupHistory()">
                                                    <i class="material-icons">history</i>
                                                    Yedekleme Geçmişi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kaydet Butonu -->
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-light me-2">İptal</button>
                                <button type="submit" class="btn bg-gradient-primary">
                                    <i class="material-icons">save</i>
                                    Ayarları Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test E-posta Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test E-postası Gönder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-outline mb-4">
                    <label class="form-label">Test E-posta Adresi</label>
                    <input type="email" class="form-control" id="testEmailAddress" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">Gönder</button>
            </div>
        </div>
    </div>
</div>

<!-- Yedekleme Geçmişi Modal -->
<div class="modal fade" id="backupHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yedekleme Geçmişi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Boyut</th>
                                <th>Tip</th>
                                <th>Durum</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="backupHistory"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bildirim Test Modal -->
<div class="modal fade" id="testNotificationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bildirim Kanallarını Test Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="channelTestResults"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Form validasyonu
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    try {
        showLoader();
        
        const formData = new FormData(this);
        
        fetch('/admin/settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Ayarlar başarıyla kaydedildi', 'success');
            } else {
                showNotification(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Ayarlar kaydedilirken hata:', error);
            showNotification('Ayarlar kaydedilirken bir hata oluştu', 'error');
        })
        .finally(() => {
            hideLoader();
        });
    } catch (error) {
        console.error('Form gönderilirken hata:', error);
        showNotification('Form gönderilirken bir hata oluştu', 'error');
        hideLoader();
    }
});

// Test e-postası gönderme
function testEmailSettings() {
    const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
    modal.show();
}

async function sendTestEmail() {
    const email = document.getElementById('testEmailAddress').value;
    
    if (!email) {
        showNotification('Lütfen bir e-posta adresi girin', 'warning');
        return;
    }
    
    try {
        showLoader();
        
        const response = await fetch('/admin/settings/test-email', {
            method: 'POST',
            body: JSON.stringify({ email }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Test e-postası başarıyla gönderildi', 'success');
            bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('E-posta gönderilirken hata:', error);
        showNotification('E-posta gönderilirken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

// Yedekleme işlemleri
async function createBackup() {
    try {
        showLoader();
        
        const response = await fetch('/admin/settings/backup/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Yedekleme başarıyla oluşturuldu', 'success');
            showBackupHistory(); // Yedekleme listesini güncelle
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('Yedekleme oluşturulurken hata:', error);
        showNotification('Yedekleme oluşturulurken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

async function showBackupHistory() {
    const modal = new bootstrap.Modal(document.getElementById('backupHistoryModal'));
    const tbody = document.getElementById('backupHistory');
    
    try {
        showLoader();
        
        const response = await fetch('/admin/settings/backup/history');
        const data = await response.json();
        
        tbody.innerHTML = '';
        
        data.backups.forEach(backup => {
            tbody.innerHTML += `
                <tr>
                    <td>${backup.created_at}</td>
                    <td>${backup.size}</td>
                    <td>${backup.type}</td>
                    <td>
                        <span class="badge badge-sm bg-gradient-${backup.status === 'completed' ? 'success' : 'warning'}">
                            ${backup.status}
                        </span>
                    </td>
                    <td>
                        <button type="button" 
                                class="btn btn-link btn-sm"
                                onclick="downloadBackup('${backup.id}')">
                            <i class="material-icons">download</i>
                        </button>
                        <button type="button" 
                                class="btn btn-link btn-sm text-danger"
                                onclick="deleteBackup('${backup.id}')">
                            <i class="material-icons">delete</i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        modal.show();
    } catch (error) {
        console.error('Yedekleme geçmişi alınırken hata:', error);
        showNotification('Yedekleme geçmişi alınırken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

async function downloadBackup(backupId) {
    try {
        showLoader();
        window.location.href = `/admin/settings/backup/${backupId}/download`;
    } catch (error) {
        console.error('Yedekleme indirilirken hata:', error);
        showNotification('Yedekleme indirilirken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

async function deleteBackup(backupId) {
    if (!confirm('Bu yedeği silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    try {
        showLoader();
        
        const response = await fetch(`/admin/settings/backup/${backupId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Yedek başarıyla silindi', 'success');
            showBackupHistory(); // Listeyi güncelle
        } else {
            showNotification(data.error, 'error');
        }
    } catch (error) {
        console.error('Yedek silinirken hata:', error);
        showNotification('Yedek silinirken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

// Bildirim kanalları testi
async function testNotificationChannels() {
    const modal = new bootstrap.Modal(document.getElementById('testNotificationModal'));
    const results = document.getElementById('channelTestResults');
    
    try {
        showLoader();
        results.innerHTML = '<div class="text-center">Test ediliyor...</div>';
        modal.show();
        
        const response = await fetch('/admin/settings/notifications/test', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        let html = '';
        Object.entries(data.results).forEach(([channel, status]) => {
            html += `
                <div class="d-flex align-items-center mb-3">
                    <div class="icon icon-shape icon-sm me-3 bg-gradient-${status ? 'success' : 'danger'} shadow text-center">
                        <i class="material-icons opacity-10">${status ? 'check' : 'close'}</i>
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="mb-1 text-dark text-sm">${channel}</h6>
                        <span class="text-xs">${status ? 'Başarılı' : 'Başarısız'}</span>
                    </div>
                </div>
            `;
        });
        
        results.innerHTML = html;
    } catch (error) {
        console.error('Bildirim kanalları test edilirken hata:', error);
        results.innerHTML = '<div class="alert alert-danger">Test sırasında bir hata oluştu</div>';
    } finally {
        hideLoader();
    }
}

// Sistem gereksinimleri kontrolü
async function checkSystemRequirements() {
    try {
        showLoader();
        
        const response = await fetch('/admin/settings/system-check');
        const data = await response.json();
        
        let message = '<h6>Sistem Kontrolü Sonuçları:</h6><ul>';
        data.checks.forEach(check => {
            message += `
                <li>
                    <span class="text-${check.status ? 'success' : 'danger'}">
                        <i class="material-icons">${check.status ? 'check_circle' : 'error'}</i>
                    </span>
                    ${check.name}: ${check.message}
                </li>
            `;
        });
        message += '</ul>';
        
        Swal.fire({
            title: 'Sistem Kontrolü',
            html: message,
            icon: data.allPassed ? 'success' : 'warning'
        });
    } catch (error) {
        console.error('Sistem kontrolü yapılırken hata:', error);
        showNotification('Sistem kontrolü yapılırken bir hata oluştu', 'error');
    } finally {
        hideLoader();
    }
}

// Form değişiklik kontrolü
let formChanged = false;
document.getElementById('settingsForm').addEventListener('change', () => {
    formChanged = true;
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Ayarları sıfırlama
function resetSettings() {
    if (confirm('Tüm ayarları varsayılan değerlerine sıfırlamak istediğinizden emin misiniz?')) {
        try {
            showLoader();
            
            fetch('/admin/settings/reset', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Ayarlar başarıyla sıfırlandı', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Ayarlar sıfırlanırken hata:', error);
                showNotification('Ayarlar sıfırlanırken bir hata oluştu', 'error');
            })
            .finally(() => {
                hideLoader();
            });
        } catch (error) {
            console.error('İstek gönderilirken hata:', error);
            showNotification('İstek gönderilirken bir hata oluştu', 'error');
            hideLoader();
        }
    }
}
</script>
@endpush