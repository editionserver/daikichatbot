<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // İlişkiler
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    // Yardımcı metodlar
    public function generateTitle()
    {
        $firstMessage = $this->messages()->where('is_bot', false)->first();
        if ($firstMessage) {
            $title = substr($firstMessage->content, 0, 50);
            $this->update(['title' => $title . (strlen($firstMessage->content) > 50 ? '...' : '')]);
        }
    }

    public function addMessage($content, $isBot = false)
    {
        $message = $this->messages()->create([
            'content' => $content,
            'is_bot' => $isBot
        ]);

        $this->touch();
        $this->update(['last_message_at' => now()]);

        if (!$this->title) {
            $this->generateTitle();
        }

        return $message;
    }

    // Export metodları
    public function exportAsTxt()
    {
        $content = '';
        foreach ($this->messages as $message) {
            $sender = $message->is_bot ? 'Bot' : 'Siz';
            $content .= "[{$message->created_at}] {$sender}:\n{$message->content}\n\n";
        }
        return $content;
    }

    public function exportAsPdf()
    {
        $pdf = \PDF::loadView('exports.chat', ['chat' => $this]);
        return $pdf->output();
    }

    // İstatistikler
    public function getStats()
    {
        return [
            'total_messages' => $this->messages()->count(),
            'user_messages' => $this->messages()->where('is_bot', false)->count(),
            'bot_messages' => $this->messages()->where('is_bot', true)->count(),
            'duration' => $this->created_at->diffInMinutes($this->last_message_at),
            'average_response_time' => $this->calculateAverageResponseTime()
        ];
    }

    protected function calculateAverageResponseTime()
    {
        $responseTimes = [];
        $messages = $this->messages()->orderBy('created_at')->get();
        
        for ($i = 1; $i < count($messages); $i++) {
            if ($messages[$i]->is_bot != $messages[$i-1]->is_bot) {
                $responseTimes[] = $messages[$i]->created_at->diffInSeconds($messages[$i-1]->created_at);
            }
        }

        return empty($responseTimes) ? 0 : array_sum($responseTimes) / count($responseTimes);
    }

    // Scopes
    public function scopeRecent($query)
    {
        return $query->orderBy('last_message_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('last_message_at', '>=', now()->subHours(1));
    }

    public function scopeInactive($query)
    {
        return $query->where('last_message_at', '<', now()->subDays(30));
    }

    // Admin metodları
    public function markAsFeatured()
    {
        $this->is_featured = true;
        $this->save();
    }

    public function markAsResolved()
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->save();
    }

    // Arama metodları
    public static function search($query)
    {
        return static::where('title', 'LIKE', "%{$query}%")
            ->orWhereHas('messages', function ($q) use ($query) {
                $q->where('content', 'LIKE', "%{$query}%");
            });
    }
}