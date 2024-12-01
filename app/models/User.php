<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',
        'profile_photo',
        'is_admin',
        'response_count'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // İlişkiler
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    // Yardımcı metodlar
    public function isProMember()
    {
        return $this->plan->name === 'Pro';
    }

    public function canSendMessage()
    {
        if ($this->isProMember()) {
            return true;
        }

        $limit = config('chat.default_plan_limit');
        return $this->response_count < $limit;
    }

    public function incrementResponseCount()
    {
        $this->response_count++;
        $this->save();
    }

    public function getInitials()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }

    public function getProfilePhotoUrl()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        return null;
    }

    // İstatistikler
    public function getChatStats()
    {
        $stats = [
            'total_chats' => $this->chats()->count(),
            'total_messages' => 0,
            'average_messages_per_chat' => 0,
            'most_active_day' => null,
        ];

        $messages = Message::whereIn('chat_id', $this->chats()->pluck('id'))
                         ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                         ->groupBy('date')
                         ->get();

        $stats['total_messages'] = $messages->sum('count');
        
        if ($stats['total_chats'] > 0) {
            $stats['average_messages_per_chat'] = round($stats['total_messages'] / $stats['total_chats'], 2);
        }

        if ($messages->count() > 0) {
            $mostActive = $messages->sortByDesc('count')->first();
            $stats['most_active_day'] = [
                'date' => $mostActive->date,
                'count' => $mostActive->count
            ];
        }

        return $stats;
    }

    // Admin metodları
    public function scopeActive($query)
    {
        return $query->where('last_login_at', '>=', now()->subDays(30));
    }

    public function scopeInactive($query)
    {
        return $query->where('last_login_at', '<', now()->subDays(30));
    }

    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}