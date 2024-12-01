@extends('layouts.admin')

@section('title', isset($response) ? 'Yanıt Düzenle' : 'Yeni Yanıt')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            {{ isset($response) ? 'Yanıt Düzenle' : 'Yeni Yanıt Oluştur' }}
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($response) ? route('admin.responses.update', $response->id) : route('admin.responses.store') }}"
                          id="responseForm"
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($response))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <!-- Sol Kolon -->
                            <div class="col-md-8">
                                <!-- Temel Bilgiler -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Temel Bilgiler</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-4">
                                                    <label class="form-label">Anahtar Kelime/Regex</label>
                                                    <input type="text" 
                                                           class="form-control @error('keyword') is-invalid @enderror"
                                                           name="keyword"
                                                           value="{{ old('keyword', $response->keyword ?? '') }}"
                                                           required>
                                                    @error('keyword')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">
                                                    Regex için /.../ formatında yazın. Örn: /^merhaba.*/i
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Yanıt Metni</label>
                                                    <textarea class="form-control @error('response_text') is-invalid @enderror"
                                                              name="response_text"
                                                              rows="6"
                                                              required>{{ old('response_text', $response->response_text ?? '') }}</textarea>
                                                    @error('response_text')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dosya Ekleri -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Dosya Ekleri</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Mevcut Dosyalar -->
                                        @if(isset($response) && $response->hasAttachments())
                                            <div class="current-attachments mb-4">
                                                <h6 class="text-sm">Mevcut Dosyalar</h6>
                                                <div class="row">
                                                    @foreach($response->attachments as $attachment)
                                                        <div class="col-md-4">
                                                            <div class="attachment-card p-2 border rounded">
                                                                @if(Str::startsWith($attachment['mime_type'], 'image/'))
                                                                    <img src="{{ asset('storage/'.$attachment['path']) }}"
                                                                         class="img-fluid rounded mb-2"
                                                                         alt="{{ $attachment['name'] }}">
                                                                @else
                                                                    <i class="material-icons">attach_file</i>
                                                                @endif
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <small class="text-muted">{{ $attachment['name'] }}</small>
                                                                    <button type="button" 
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="removeAttachment('{{ $attachment['path'] }}')">
                                                                        <i class="material-icons">delete</i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Yeni Dosya Yükleme -->
                                        <div class="file-upload-area">
                                            <div class="dropzone-container border rounded p-4 text-center">
                                                <input type="file" 
                                                       name="attachments[]" 
                                                       multiple 
                                                       class="file-input d-none"
                                                       accept="image/*,application/pdf">
                                                <div class="dz-message">
                                                    <i class="material-icons display-4">cloud_upload</i>
                                                    <h6>Dosyaları sürükleyin veya seçin</h6>
                                                    <p class="text-sm text-muted">
                                                        PNG, JPG, GIF veya PDF. Maksimum 5MB.
                                                    </p>
                                                </div>
                                            </div>
                                            <div id="preview" class="mt-3 row"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ Kolon -->
                            <div class="col-md-4">
                                <!-- Ayarlar -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Ayarlar</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="is_active" 
                                                   value="1"
                                                   {{ (old('is_active', $response->is_active ?? true)) ? 'checked' : '' }}>
                                            <label class="custom-control-label">Aktif</label>
                                        </div>

                                        <div class="form-group">
                                            <label>Öncelik</label>
                                            <select class="form-control" name="priority">
                                                <option value="1" {{ (old('priority', $response->priority ?? '') == 1) ? 'selected' : '' }}>
                                                    Düşük
                                                </option>
                                                <option value="2" {{ (old('priority', $response->priority ?? '') == 2) ? 'selected' : '' }}>
                                                    Normal
                                                </option>
                                                <option value="3" {{ (old('priority', $response->priority ?? '') == 3) ? 'selected' : '' }}>
                                                    Yüksek
                                                </option>
                                            </select>
                                        </div>

                                        <hr>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="case_sensitive" 
                                                   value="1"
                                                   {{ (old('case_sensitive', $response->case_sensitive ?? false)) ? 'checked' : '' }}>
                                            <label class="custom-control-label">Büyük/Küçük Harf Duyarlı</label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="exact_match" 
                                                   value="1"
                                                   {{ (old('exact_match', $response->exact_match ?? false)) ? 'checked' : '' }}>
                                            <label class="custom-control-label">Tam Eşleşme</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- İstatistikler -->
                                @if(isset($response))
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">İstatistikler</h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Oluşturulma:</strong> {{ $response->created_at->format('d.m.Y H:i') }}</p>
                                            <p><strong>Son Güncelleme:</strong> {{ $response->updated_at->format('d.m.Y H:i') }}</p>
                                            <p><strong>Kullanım Sayısı:</strong> {{ $response->getUsageCount() }}</p>
                                            <p><strong>Son Kullanım:</strong> {{ $response->last_used_at ? $response->last_used_at->format('d.m.Y H:i') : '-' }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Kaydet Butonu -->
                                <div class="text-center mt-4">
                                    <a href="{{ route('admin.responses.index') }}" class="btn btn-light me-2">İptal</a>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        {{ isset($response) ? 'Güncelle' : 'Kaydet' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yardım Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Anahtar Kelime Kullanımı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Normal Anahtar Kelime</h6>
                <p>Basit metin eşleştirmesi için normal kelime veya kelime grupları kullanın.</p>
                <ul>
                    <li><code>merhaba</code> - "merhaba" kelimesini içeren mesajları eşleştirir</li>
                    <li><code>klima ayarları</code> - "klima ayarları" ifadesini içeren mesajları eşleştirir</li>
                </ul>

                <hr>

                <h6>Regex (Düzenli İfadeler)</h6>
                <p>Daha karmaşık eşleştirmeler için regex kullanabilirsiniz. Regex desenini / işaretleri arasına yazın.</p>
                <ul>
                    <li><code>/^merhaba.*/i</code> - "merhaba" ile başlayan mesajları eşleştirir (büyük/küçük harf duyarsız)</li>
                    <li><code>/\bklima\b/</code> - "klima" kelimesini tam eşleştirme ile bulur</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dosya yükleme alanı
const dropZone = document.querySelector('.dropzone-container');
const fileInput = document.querySelector('.file-input');
const preview = document.getElementById('preview');

// Sürükle-bırak olayları
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-primary');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary');
    
    const files = e.dataTransfer.files;
    handleFiles(files);
});

dropZone.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            showNotification('Dosya boyutu 5MB\'dan büyük olamaz', 'error');
            return;
        }

        const reader = new FileReader();
        const div = document.createElement('div');
        div.className = 'col-md-4 mb-3';
        
        reader.onload = (e) => {
            div.innerHTML = `
                <div class="preview-item border rounded p-2">
                    ${file.type.startsWith('image/') 
                        ? `<img src="${e.target.result}" class="img-fluid rounded mb-2" alt="${file.name}">`
                        : `<i class="material-icons">description</i>`
                    }
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${file.name}</small>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.col-md-4').remove()">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                </div>
            `;
        };
        
        reader.readAsDataURL(file);
        preview.appendChild(div);
    });
}

// Mevcut dosya eklerini silme
function removeAttachment(path) {
    if (confirm('Bu dosyayı silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/responses/attachments/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const element = document.querySelector(`[data-path="${path}"]`).closest('.col-md-4');
                element.remove();
                showNotification('Dosya başarıyla silindi', 'success');
            } else {
                showNotification('Dosya silinirken bir hata oluştu', 'error');
            }
        });
    }
}

// Form validasyonu
document.getElementById('responseForm').addEventListener('submit', function(e) {
    const keyword = document.querySelector('input[name="keyword"]').value;
    const responseText = document.querySelector('textarea[name="response_text"]').value;

    // Anahtar kelime kontrolü
    if (keyword.trim() === '') {
        e.preventDefault();
        showNotification('Anahtar kelime boş olamaz', 'error');
        return;
    }

    // Regex kontrolü
    if (keyword.startsWith('/')) {
        try {
            new RegExp(keyword.slice(1, -1));
        } catch (error) {
            e.preventDefault();
            showNotification('Geçersiz regex deseni', 'error');
            return;
        }
    }

    // Yanıt metni kontrolü
    if (responseText.trim() === '') {
        e.preventDefault();
        showNotification('Yanıt metni boş olamaz', 'error');
        return;
    }
});

// Anahtar kelime yardımı
const keywordInput = document.querySelector('input[name="keyword"]');
const helpIcon = document.createElement('i');
helpIcon.className = 'material-icons position-absolute end-0 top-50 translate-middle-y pe-2 text-primary';
helpIcon.textContent = 'help_outline';
helpIcon.style.cursor = 'pointer';

helpIcon.addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('helpModal'));
    modal.show();
});

keywordInput.parentElement.appendChild(helpIcon);

// Regex otomatik tamamlama
keywordInput.addEventListener('input', function() {
    if (this.value.startsWith('/') && !this.value.endsWith('/')) {
        this.dataset.isRegex = 'true';
    } else if (this.dataset.isRegex === 'true' && !this.value.startsWith('/')) {
        this.dataset.isRegex = 'false';
    }
});

keywordInput.addEventListener('keyup', function(e) {
    if (e.key === '/') {
        if (this.value === '/') {
            this.dataset.isRegex = 'true';
        }
    }
});

keywordInput.addEventListener('blur', function() {
    if (this.dataset.isRegex === 'true' && !this.value.endsWith('/')) {
        this.value = this.value + '/';
    }
});

// Dosya yükleme limiti kontrolü
function checkFileUploadLimit() {
    const maxFiles = 10;
    const currentFiles = document.querySelectorAll('.preview-item').length;
    
    if (currentFiles >= maxFiles) {
        showNotification(`En fazla ${maxFiles} dosya ekleyebilirsiniz`, 'warning');
        return false;
    }
    return true;
}

// Form içeriğini otomatik kaydetme
let autoSaveTimeout;
const formElements = document.querySelectorAll('input, textarea, select');

formElements.forEach(element => {
    element.addEventListener('input', () => {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            saveFormDraft();
        }, 3000);
    });
});

function saveFormDraft() {
    const formData = new FormData(document.getElementById('responseForm'));
    formData.append('draft', true);

    fetch('/admin/responses/save-draft', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Taslak otomatik kaydedildi', 'info', 2000);
        }
    });
}

// Taslak yükleme
window.addEventListener('load', () => {
    const draftId = localStorage.getItem('responseFormDraftId');
    if (draftId) {
        loadFormDraft(draftId);
    }
});

function loadFormDraft(draftId) {
    fetch(`/admin/responses/load-draft/${draftId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Object.keys(data.draft).forEach(key => {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = data.draft[key];
                    }
                });
                showNotification('Taslak yüklendi', 'success');
            }
        });
}

// Önizleme
const previewButton = document.createElement('button');
previewButton.type = 'button';
previewButton.className = 'btn btn-info mb-3';
previewButton.innerHTML = '<i class="material-icons">preview</i> Önizleme';
previewButton.addEventListener('click', showPreview);

document.querySelector('.card-body').insertBefore(previewButton, document.querySelector('.row'));

function showPreview() {
    const responseText = document.querySelector('textarea[name="response_text"]').value;
    const attachments = Array.from(document.querySelectorAll('.preview-item')).map(item => ({
        name: item.querySelector('small').textContent,
        type: item.querySelector('img') ? 'image' : 'file',
        url: item.querySelector('img')?.src
    }));

    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const modalBody = document.querySelector('#previewModal .modal-body');
    
    modalBody.innerHTML = `
        <div class="message bot">
            <div class="message-content">
                ${responseText}
                ${attachments.map(attachment => `
                    <div class="attachment">
                        ${attachment.type === 'image' 
                            ? `<img src="${attachment.url}" class="img-fluid rounded" alt="${attachment.name}">`
                            : `<i class="material-icons">attach_file</i> ${attachment.name}`
                        }
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    modal.show();
}
</script>
@endpush

<!-- Önizleme Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yanıt Önizleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>