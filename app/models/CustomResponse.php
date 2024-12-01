<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomResponse extends Model
{
    protected $fillable = [
        'keyword',
        'response_text',
        'attachments',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_active' => 'boolean'
    ];

    // Yardımcı metodlar
    public static function findByKeyword($message)
    {
        $keywords = self::where('is_active', true)
                       ->orderBy('priority', 'desc')
                       ->get();

        foreach ($keywords as $response) {
            if (stripos($message, $response->keyword) !== false) {
                return $response;
            }
        }

        return null;
    }

    public function addAttachment($path, $originalName)
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = [
            'path' => $path,
            'name' => $originalName,
            'created_at' => now()
        ];
        
        $this->attachments = $attachments;
        $this->save();
    }

    public function removeAttachment($path)
    {
        $attachments = $this->attachments ?? [];
        $this->attachments = array_filter($attachments, function ($attachment) use ($path) {
            return $attachment['path'] !== $path;
        });
        $this->save();

        // Dosyayı fiziksel olarak sil
        \Storage::disk('public')->delete($path);
    }

    // Regex destek metodları
    public function hasRegexKeyword()
    {
        return preg_match('/^\/.*\/$/', $this->keyword);
    }

    public function matchesMessage($message)
    {
        if ($this->hasRegexKeyword()) {
            return preg_match($this->keyword, $message);
        }
        
        return stripos($message, $this->keyword) !== false;
    }

    // Admin metodları
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        $this->save();
    }

    // İstatistik metodları
    public function getUsageCount()
    {
        return Cache::remember('custom_response_usage_'.$this->id, 3600, function () {
            return Message::where('content', 'LIKE', '%'.$this->response_text.'%')
                         ->where('is_bot', true)
                         ->count();
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrioritized($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachments');
    }

    // Export/Import metodları
    public function toArray()
    {
        $array = parent::toArray();
        $array['usage_count'] = $this->getUsageCount();
        return $array;
    }

    public static function importFromArray($data)
    {
        $response = new self();
        $response->fill($data);
        $response->save();

        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                if (isset($attachment['content']) && isset($attachment['name'])) {
                    $path = 'responses/' . uniqid() . '_' . $attachment['name'];
                    \Storage::disk('public')->put($path, base64_decode($attachment['content']));
                    $response->addAttachment($path, $attachment['name']);
                }
            }
        }

        return $response;
    }

    public function exportWithAttachments()
    {
        $data = $this->toArray();
        
        if (!empty($this->attachments)) {
            foreach ($this->attachments as &$attachment) {
                $content = \Storage::disk('public')->get($attachment['path']);
                $attachment['content'] = base64_encode($content);
            }
        }

        return $data;
    }

    // Validasyon metodları
    public function validate()
    {
        $errors = [];

        if (empty($this->keyword)) {
            $errors[] = 'Anahtar kelime boş olamaz';
        }

        if (empty($this->response_text)) {
            $errors[] = 'Yanıt metni boş olamaz';
        }

        if ($this->hasRegexKeyword()) {
            try {
                preg_match($this->keyword, 'test');
            } catch (\Exception $e) {
                $errors[] = 'Geçersiz regex kalıbı';
            }
        }

        return $errors;
    }

    // Webhook desteği
    public function hasWebhook()
    {
        return !empty($this->webhook_url);
    }

    public function triggerWebhook($message)
    {
        if (!$this->hasWebhook()) {
            return null;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($this->webhook_url, [
                'json' => [
                    'message' => $message,
                    'keyword' => $this->keyword,
                    'timestamp' => now()
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Webhook hatası: ' . $e->getMessage());
            return null;
        }
    }

    // Cache metodları
    public function clearCache()
    {
        Cache::forget('custom_response_usage_' . $this->id);
    }

    // Versiyonlama desteği
    public function createVersion()
    {
        return CustomResponseVersion::create([
            'custom_response_id' => $this->id,
            'keyword' => $this->keyword,
            'response_text' => $this->response_text,
            'attachments' => $this->attachments,
            'version' => $this->version + 1
        ]);
    }

    public function restoreVersion($versionId)
    {
        $version = CustomResponseVersion::findOrFail($versionId);
        $this->keyword = $version->keyword;
        $this->response_text = $version->response_text;
        $this->attachments = $version->attachments;
        $this->version = $version->version;
        $this->save();
    }

    // API metodları
    public function toApiResponse()
    {
        return [
            'id' => $this->id,
            'keyword' => $this->keyword,
            'response' => [
                'text' => $this->response_text,
                'attachments' => $this->formatAttachmentsForApi()
            ],
            'metadata' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'usage_count' => $this->getUsageCount()
            ]
        ];
    }

    protected function formatAttachmentsForApi()
    {
        if (empty($this->attachments)) {
            return [];
        }

        return array_map(function ($attachment) {
            return [
                'name' => $attachment['name'],
                'url' => \Storage::disk('public')->url($attachment['path']),
                'type' => $this->getAttachmentType($attachment['name'])
            ];
        }, $this->attachments);
    }

    protected function getAttachmentType($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $typeMap = [
            'pdf' => 'document',
            'doc' => 'document',
            'docx' => 'document',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image'
        ];

        return $typeMap[strtolower($extension)] ?? 'other';
    }
}