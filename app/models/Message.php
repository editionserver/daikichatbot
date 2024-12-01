<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'content',
        'is_bot',
        'metadata'
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'metadata' => 'array'
    ];

    // İlişkiler
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Yardımcı metodlar
    public function attachFiles(array $files)
    {
        $attachments = [];
        foreach ($files as $file) {
            $path = $file->store('chat-attachments', 'public');
            $attachments[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ];
        }

        $metadata = $this->metadata ?? [];
        $metadata['attachments'] = $attachments;
        $this->metadata = $metadata;
        $this->save();

        return $attachments;
    }

    public function getAttachments()
    {
        return $this->metadata['attachments'] ?? [];
    }

    public function hasAttachments()
    {
        return !empty($this->getAttachments());
    }

    // İçerik işleme metodları
    public function getFormattedContent()
    {
        return nl2br(e($this->content));
    }

    public function getSummary($length = 100)
    {
        return strlen($this->content) > $length 
            ? substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    // Metadata işleme
    public function addMetadata($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMetadata($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    // Analitik metodları
    public function getWordCount()
    {
        return str_word_count($this->content);
    }

    public function getCharacterCount()
    {
        return strlen($this->content);
    }

    public function getSentimentScore()
    {
        // Basit bir duygu analizi puanı (gerçek uygulamada NLP servisi kullanılabilir)
        $positiveWords = ['teşekkür', 'harika', 'mükemmel', 'iyi', 'güzel'];
        $negativeWords = ['kötü', 'hata', 'sorun', 'problem', 'yetersiz'];

        $content = mb_strtolower($this->content);
        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($content, $word);
        }

        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($content, $word);
        }

        if ($positiveCount + $negativeCount === 0) {
            return 0;
        }

        return ($positiveCount - $negativeCount) / ($positiveCount + $negativeCount);
    }

    // Scopes
    public function scopeFromBot($query)
    {
        return $query->where('is_bot', true);
    }

    public function scopeFromUser($query)
    {
        return $query->where('is_bot', false);
    }

    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('metadata->attachments');
    }

    // Admin metodları
    public function flag($reason)
    {
        $this->addMetadata('flagged', [
            'reason' => $reason,
            'timestamp' => now()
        ]);
    }

    public function unflag()
    {
        $metadata = $this->metadata;
        unset($metadata['flagged']);
        $this->metadata = $metadata;
        $this->save();
    }

    public function isFlagged()
    {
        return isset($this->metadata['flagged']);
    }
}