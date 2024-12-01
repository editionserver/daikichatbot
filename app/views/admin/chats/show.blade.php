@extends('layouts.admin')

@section('title', 'Sohbet Detayı #' . $chat->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sol Kolon - Sohbet Detayları -->
        <div class="col-lg-4">
            <!-- Kullanıcı Bilgileri -->
            <div class="card mb-4">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <div class="text-end pt-1">
                        <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="text-center mb-4">
                        @if($chat->user && $chat->user->profile_photo)
                            <img src="{{ asset('storage/'.$chat->user->profile_photo) }}" 
                                 class="avatar avatar-xl rounded-circle" 
                                 alt="{{ $chat->user->name }}">
                        @else
                            <div class="avatar avatar-xl bg-gradient-primary rounded-circle">
                                {{ $chat->user ? $chat->user->getInitials() : 'M' }}
                            </div>
                        @endif
                        <h6 class="mt-3 mb-0">
                            {{ $chat->user ? $chat->user->name : 'Misafir Kullanıcı' }}
                        </h6>
                        @if($chat->user)
                            <p class="text-sm text-secondary mb-0">{{ $chat->user->email }}</p>
                            <span class="badge bg-gradient-{{ $chat->user->plan->isPro() ? 'success' : 'secondary' }}">
                                {{ $chat->user->plan->name }}
                            </span>
                        @endif
                    </div>

                    @if($chat->user)
                    <hr class="horizontal dark">
                    <div class="row">
                        <div class="col-4">
                            <h6>{{ $chat->user->chats_count }}</h6>
                            <p class="text-xs text-secondary mb-0">Sohbet</p>
                        </div>
                        <div class="col-4">
                            <h6>{{ $chat->user->messages_count }}</h6>
                            <p class="text-xs text-secondary mb-0">Mesaj</p>
                        </div>
                        <div class="col-4">
                            <h6>{{ $chat->user->created_at->diffForHumans(null, true) }}</h6>
                            <p class="text-xs text-secondary mb-0">Üyelik</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sohbet İstatistikleri -->
            <div class="card mb-4">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <div class="text-end pt-1">
                        <h5 class="mb-0">Sohbet İstatistikleri</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="text-center">
                                <h6 class="mb-0">{{ $chat->messages_count }}</h6>
                                <p class="text-xs text-secondary mb-0">Toplam Mesaj</p>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="text-center">
                                <h6 class="mb-0">{{ $chat->getDuration() }}</h6>
                                <p class="text-xs text-secondary mb-0">Süre</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h6 class="mb-0">{{ number_format($chat->getAverageResponseTime(), 1) }}s</h6>
                                <p class="text-xs text-secondary mb-0">Ort. Yanıt Süresi</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h6 class="mb-0">{{ number_format($chat->getSentimentScore() * 100, 1) }}%</h6>
                                <p class="text-xs text-secondary mb-0">Memnuniyet</p>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark">

                    <!-- Duygu Analizi -->
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Duygu Analizi</h6>
                    <div class="progress-wrapper">
                        <div class="progress-info">
                            <div class="progress-percentage">
                                <span class="text-sm font-weight-bold">Pozitif</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $chat->getPositiveSentimentPercentage() }}%"></div>
                        </div>
                    </div>
                    <div class="progress-wrapper">
                        <div class="progress-info">
                            <div class="progress-percentage">
                                <span class="text-sm font-weight-bold">Nötr</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $chat->getNeutralSentimentPercentage() }}%"></div>
                        </div>
                    </div>
                    <div class="progress-wrapper">
                        <div class="progress-info">
                            <div class="progress-percentage">
                                <span class="text-sm font-weight-bold">Negatif</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: {{ $chat->getNegativeSentimentPercentage() }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sık Kullanılan Kelimeler -->
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">workspaces</i>
                    </div>
                    <div class="text-end pt-1">
                        <h5 class="mb-0">Sık Kullanılan Kelimeler</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="frequent-words">
                        @foreach($chat->getFrequentWords() as $word => $count)
                            <span class="badge bg-light text-dark me-2 mb-2" style="font-size: {{ 12 + min($count, 8) }}px">
                                {{ $word }} ({{ $count }})
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon - Sohbet Mesajları -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Sohbet Mesajları</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="dropdown">
                                <button class="btn bg-gradient-dark dropdown-toggle mb-0" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="material-icons">file_download</i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.chats.export', ['id' => $chat->id, 'format' => 'pdf']) }}">
                                            <i class="material-icons">picture_as_pdf</i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.chats.export', ['id' => $chat->id, 'format' => 'txt']) }}">
                                            <i class="material-icons">description</i> TXT
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="messages-container">
                        @foreach($chat->messages as $message)
                            <div class="message {{ $message->is_bot ? 'bot' : 'user' }}">
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">
                                            {{ $message->is_bot ? 'Bot' : ($chat->user ? $chat->user->name : 'Misafir') }}
                                        </span>
                                        <span class="message-time">
                                            {{ $message->created_at->format('d.m.Y H:i:s') }}
                                        </span>
                                    </div>
                                    <div class="message-body">
                                        {!! nl2br(e($message->content)) !!}
                                    </div>
                                    @if($message->hasAttachments())
                                        <div class="message-attachments">
                                            @foreach($message->getAttachments() as $attachment)
                                                <div class="attachment">
                                                    @if(Str::startsWith($attachment['mime_type'], 'image/'))
                                                        <img src="{{ asset('storage/'.$attachment['path']) }}" 
                                                             alt="{{ $attachment['name'] }}"
                                                             class="img-fluid rounded">
                                                    @else
                                                        <a href="{{ asset('storage/'.$attachment['path']) }}" 
                                                           target="_blank"
                                                           class="attachment-link">
                                                            <i class="material-icons">attach_file</i>
                                                            {{ $attachment['name'] }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($message->metadata)
                                        <div class="message-metadata">
                                            <small class="text-muted">
                                                <i class="material-icons">info</i>
                                                Model: {{ $message->metadata['model'] ?? 'N/A' }},
                                                Tokens: {{ $message->metadata['tokens'] ?? 'N/A' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.messages-container {
    max-height: 600px;
    overflow-y: auto;
    padding: 1rem;
}

.message {
    margin-bottom: 1.5rem;
    max-width: 80%;
}

.message.user {
    margin-left: auto;
}

.message-content {
    padding: 1rem;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
}

.message.bot .message-content {
    background-color: #e3f2fd;
}

.message.user .message-content {
    background-color: #e8f5e9;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.message-sender {
    font-weight: 600;
    font-size: 0.875rem;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.message-attachments {
    margin-top: 0.5rem;
    border-top: 1px solid rgba(0,0,0,0.1);
    padding-top: 0.5rem;
}

.attachment {
    margin-bottom: 0.5rem;
}

.attachment-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background-color: rgba(0,0,0,0.05);
    border-radius: 0.25rem;
    color: inherit;
    text-decoration: none;
}

.message-metadata {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #6c757d;
}
</style>

@endsection

@push('scripts')
<script>
// Mesaj konteynerini en alta kaydır
const messagesContainer = document.querySelector('.messages-container');
messagesContainer.scrollTop = messagesContainer.scrollHeight;

// Resim görüntüleyici
document.querySelectorAll('.message-attachments img').forEach(img => {
    img.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        document.getElementById('modalImage').src = this.src;
        modal.show();
    });
});

// Sık kullanılan kelimeleri yeniden boyutlandır
document.querySelectorAll('.frequent-words .badge').forEach(badge => {
    const count = parseInt(badge.textContent.match(/\((\d+)\)/)[1]);
    const minSize = 12;
    const maxSize = 20;
    const size = minSize + Math.min(count, maxSize - minSize);
    badge.style.fontSize = `${size}px`;
});
</script>
@endpush